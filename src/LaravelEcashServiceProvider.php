<?php

namespace IXCoders\LaravelEcash;

use Illuminate\Routing\Router;
use IXCoders\LaravelEcash\Http\Middleware\VerifyRemoteHostForCallback;
use IXCoders\LaravelEcash\Http\Middleware\VerifyResponseToken;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelEcashServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-ecash-sdk')
            ->hasConfigFile('laravel-ecash-sdk');
    }

    public function packageRegistered()
    {
        $this->app->bind('ecash.laravel', function () {
            return new \IXCoders\LaravelEcash\EcashManager();
        });

        $this->publishes([
            __DIR__ . '/../config/laravel-ecash-sdk.php' => config_path('laravel-ecash-sdk.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/create_ecash_transaction_logs_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_ecash_transaction_logs_table.php'),
        ], 'migrations');

        $use_default_controller = config('laravel-ecash-sdk.use_default_controller');
        if ($use_default_controller) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');
        }

        $route = $this->app->make(Router::class);
        $route->aliasMiddleware('ecash.verify_remote_host', VerifyRemoteHostForCallback::class);
        $route->middleware('ecash.verify_remote_host');

        $route->aliasMiddleware('ecash.verify_response_token', VerifyResponseToken::class);
        $route->middleware('ecash.verify_response_token');
    }
}
