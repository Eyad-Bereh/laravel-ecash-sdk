<?php

namespace IXCoders\LaravelEcash;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use IXCoders\LaravelEcash\Exceptions\InvalidAmountException;
use IXCoders\LaravelEcash\Exceptions\InvalidCheckoutTypeException;
use IXCoders\LaravelEcash\Exceptions\InvalidCurrencyException;
use IXCoders\LaravelEcash\Exceptions\InvalidOrMissingConfigurationValueException;
use IXCoders\LaravelEcash\Exceptions\MissingRouteException;

class EcashManager
{
    private string $terminal_key;

    private string $merchant_id;

    private string $merchant_secret;

    private string $redirect_url;

    private string $callback_url;

    private static $transaction_log_model = 'IXCoders\\LaravelEcash\\Models\\EcashTransactionLog';

    public $transaction;

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
            $is_valid = $this->checkIfConfigurationValueIsSet($key);
            if (!$is_valid) {
                throw new InvalidOrMissingConfigurationValueException($key);
            }
        }

        $routes = ['redirect_route', 'callback_route'];

        $length = count($routes);
        for ($i = 0; $i < $length; $i++) {
            $route = $routes[$i];
            $route_name = config('laravel-ecash-sdk.' . $route);
            if (is_null($route_name) || !Route::has($route_name)) {
                throw new MissingRouteException($route_name);
            }
        }

        $this->terminal_key = config('laravel-ecash-sdk.terminal_key');
        $this->merchant_id = config('laravel-ecash-sdk.merchant_id');
        $this->merchant_secret = config('laravel-ecash-sdk.merchant_secret');

        $redirect_route = config('laravel-ecash-sdk.redirect_route');
        $callback_route = config('laravel-ecash-sdk.callback_route');

        $this->redirect_url = route($redirect_route);
        $this->callback_url = route($callback_route);
    }

    public function getVerificationCode(int $amount, string $reference): string
    {
        $combination = $this->merchant_id .
            $this->merchant_secret .
            $amount .
            mb_convert_encoding($reference, 'ASCII', 'UTF-8');

        $hash = md5($combination);

        return Str::upper($hash);
    }

    public function checkVerificationCode(string $hash, int $amount, string $reference): bool
    {
        $current = $this->getVerificationCode($amount, $reference);
        $hash = Str::upper($hash);

        return strcmp($current, $hash);
    }

    public function generatePaymentLink(string $checkout_type, int $amount, string $reference, string $currency = 'SYP', ?string $language = null): string
    {
        if (!$this->isValidCheckoutType($checkout_type)) {
            throw new InvalidCheckoutTypeException($checkout_type);
        }

        if (!$this->isValidCurrency($currency)) {
            throw new InvalidCurrencyException($currency);
        }

        if ($amount <= 0) {
            throw new InvalidAmountException($amount);
        }

        if (is_null($language)) {
            $language = App::getLocale();
        }

        $verification_code = $this->getVerificationCode($amount, $reference);

        $base_url = 'https://checkout.ecash-pay.co/';
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

        $payment_link = $base_url . $params;

        $this->transaction = $this->storeTransactionLogEntry($checkout_type, $amount, $reference, $currency, $language);

        return $payment_link;
    }

    private function isValidCheckoutType(string $value): bool
    {
        $value = Str::upper($value);
        $checkout_types = config('laravel-ecash-sdk.checkout_types');

        return in_array($value, $checkout_types);
    }

    private function isValidCurrency(string $value): bool
    {
        $value = Str::upper($value);
        $currencies = config('laravel-ecash-sdk.currencies');

        return in_array($value, $currencies);
    }

    private function checkIfConfigurationValueIsSet(string $key): bool
    {
        $option = 'laravel-ecash-sdk.' . $key;
        $value = config($option);
        return !is_null($value);
    }

    private function storeTransactionLogEntry(string $checkout_type, int $amount, string $reference, string $currency = 'SYP', ?string $language = null)
    {
        $transaction = new static::$transaction_log_model;
        $transaction->checkout_type = $checkout_type;
        $transaction->amount = $amount;
        $transaction->reference = $reference;
        $transaction->currency = $currency;
        $transaction->language = $language;
        $transaction->verification_code = $this->getVerificationCode($amount, $reference);
        $transaction->save();

        return $transaction;
    }

    public function getTransactionLogEntry()
    {
        return $this->transaction;
    }

    public static function useEcashTransactionLogModel($model): void
    {
        static::$transaction_log_model = $model;
    }

    public static function getEcashTransactionLogModel(): string
    {
        return static::$transaction_log_model;
    }
}
