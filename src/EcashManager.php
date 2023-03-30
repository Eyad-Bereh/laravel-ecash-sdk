<?php

namespace IXCoders\LaravelEcash;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use IXCoders\LaravelEcash\Events\EcashTransactionCompleted;
use IXCoders\LaravelEcash\Exceptions\EcashTransactionSaveFailedException;
use IXCoders\LaravelEcash\Exceptions\InvalidAmountException;
use IXCoders\LaravelEcash\Exceptions\InvalidCheckoutTypeException;
use IXCoders\LaravelEcash\Exceptions\InvalidCurrencyException;
use IXCoders\LaravelEcash\Exceptions\InvalidOrMissingConfigurationValueException;
use IXCoders\LaravelEcash\Exceptions\InvalidRouteMethodException;
use IXCoders\LaravelEcash\Exceptions\InvalidTokenException;
use IXCoders\LaravelEcash\Exceptions\MissingMiddlewareException;
use IXCoders\LaravelEcash\Exceptions\MissingRouteException;
use IXCoders\LaravelEcash\Http\Middleware\VerifyRemoteHostForCallback;
use IXCoders\LaravelEcash\Http\Middleware\VerifyResponseToken;
use IXCoders\LaravelEcash\Utilities\ConfigurationValidator;
use IXCoders\LaravelEcash\Utilities\RequestDataTransformer;
use IXCoders\LaravelEcash\Utilities\VerificationCodeManager;
use IXCoders\LaravelEcash\Utilities\VerificationTokenManager;

class EcashManager
{
    private string $terminal_key;

    private string $merchant_id;

    private string $merchant_secret;

    private string $redirect_url;

    private string $callback_url;

    private static string $transaction_model = 'IXCoders\\LaravelEcash\\EcashTransaction';

    private Model $transaction;

    private VerificationCodeManager $vcm;

    private VerificationTokenManager $vtm;

    private RequestDataTransformer $rdt;

    public function __construct()
    {
        $keys = [
            'terminal_key',
            'merchant_id',
            'merchant_secret',
        ];

        $length = count($keys);
        for ($i = 0; $i < $length; $i++) {
            $key = $keys[$i];
            $is_valid = ConfigurationValidator::checkIfConfigurationValueIsSet($key);
            if (! $is_valid) {
                throw new InvalidOrMissingConfigurationValueException($key);
            }
        }

        $routes = ['redirect_route', 'callback_route'];

        $length = count($routes);
        for ($i = 0; $i < $length; $i++) {
            $route = $routes[$i];
            $route_name = config('laravel-ecash-sdk.'.$route);
            if (is_null($route_name) || ! Route::has($route_name)) {
                throw new MissingRouteException($route_name);
            }
        }

        $this->terminal_key = config('laravel-ecash-sdk.terminal_key');
        $this->merchant_id = config('laravel-ecash-sdk.merchant_id');
        $this->merchant_secret = config('laravel-ecash-sdk.merchant_secret');

        $this->vcm = new VerificationCodeManager($this->merchant_id, $this->merchant_secret);
        $this->vtm = new VerificationTokenManager($this->merchant_id, $this->merchant_secret);
        $this->rdt = new RequestDataTransformer();

        $redirect_route = config('laravel-ecash-sdk.redirect_route');
        $callback_route = config('laravel-ecash-sdk.callback_route');

        $this->redirect_url = route($redirect_route);
        $this->callback_url = route($callback_route);

        $callback_route_methods = Route::getRoutes()->getByName($callback_route)->methods();
        if (! in_array('POST', $callback_route_methods)) {
            throw new InvalidRouteMethodException($callback_route, 'POST', $callback_route_methods);
        }

        $redirect_route_methods = Route::getRoutes()->getByName($redirect_route)->methods();
        if (! in_array('GET', $redirect_route_methods)) {
            throw new InvalidRouteMethodException($redirect_route, 'GET', $redirect_route_methods);
        }

        $callback_route_middlewares = Route::getRoutes()->getByName($callback_route)->gatherMiddleware();
        $middlewares = [
            'ecash.verify_remote_host' => VerifyRemoteHostForCallback::class,
            'ecash.verify_response_token' => VerifyResponseToken::class,
        ];

        foreach ($middlewares as $alias => $class) {
            if (! in_array($alias, $callback_route_middlewares)) {
                throw new MissingMiddlewareException($callback_route, $class, $alias);
            }
        }
    }

    public function generatePaymentLink(string $checkout_type, string $amount, string $reference, string $currency = 'SYP', ?string $language = null): string
    {
        if (! ConfigurationValidator::isValidCheckoutType($checkout_type)) {
            throw new InvalidCheckoutTypeException($checkout_type);
        }

        if (! ConfigurationValidator::isValidCurrency($currency)) {
            throw new InvalidCurrencyException($currency);
        }

        if ($amount <= 0) {
            throw new InvalidAmountException($amount);
        }

        if (is_null($language)) {
            $language = App::getLocale();
        }

        $verification_code = $this->vcm->getVerificationCode($amount, $reference);

        $base_url = 'https://checkout.ecash-pay.com/';
        $segments = [
            'Checkout',
            Str::studly($checkout_type),
            $this->terminal_key,
            $this->merchant_id,
            Str::upper($verification_code),
            Str::upper($currency),
            $amount,
            Str::upper($language),
            htmlspecialchars($reference),
            urlencode($this->redirect_url),
            urlencode($this->callback_url),
        ];
        $params = implode('/', $segments);

        $payment_link = $base_url.$params;

        $this->transaction = $this->storeTransactionEntry($checkout_type, $amount, $reference, $currency, $language);

        return $payment_link;
    }

    private function storeTransactionEntry(string $checkout_type, string $amount, string $reference, string $currency = 'SYP', ?string $language = null)
    {
        $model = static::$transaction_model;
        $verification_code = $this->vcm->getVerificationCode($amount, $reference);
        $exists = $model::where('verification_code', $verification_code)->exists();

        if (! $exists) {
            $transaction = new $model;
            $transaction->checkout_type = $checkout_type;
            $transaction->amount = $amount;
            $transaction->reference = $reference;
            $transaction->currency = $currency;
            $transaction->language = $language;
            $transaction->verification_code = $this->vcm->getVerificationCode($amount, $reference);
            $result = $transaction->save();
            if ($result === false) {
                throw new EcashTransactionSaveFailedException();
            }
        } else {
            $transaction = $model::where('verification_code', $verification_code)->first();
        }

        return $transaction;
    }

    public function updateTransactionEntry(array $data, array $additional = []): bool
    {
        $data = $this->rdt->transformDataArrayFromRequest($data);
        $token = $data['token'];
        $transaction_number = $data['transaction_number'];
        $amount = $data['Amount'];
        $reference = $data['OrderRef'];

        unset($data['Amount']);
        unset($data['OrderRef']);

        $isValidToken = $this->vtm->checkVerificationToken($token, $transaction_number, $amount, $reference);
        if (! $isValidToken) {
            throw new InvalidTokenException($token);
        }

        $attributes = array_merge($additional, $data);

        $verification_code = $this->vcm->getVerificationCode($amount, $reference);
        $model = static::$transaction_model;

        $transaction = $model::where('verification_code', $verification_code)->firstOrFail();

        $status = $transaction->update($attributes);
        if ($status === false) {
            throw new EcashTransactionSaveFailedException();
        }
        event(new EcashTransactionCompleted($transaction));

        return $status;
    }

    public function getCurrentTransactionEntry()
    {
        return $this->transaction;
    }

    public static function useEcashTransactionModel($model): void
    {
        static::$transaction_model = $model;
    }

    public static function getEcashTransactionModel(): string
    {
        return static::$transaction_model;
    }
}
