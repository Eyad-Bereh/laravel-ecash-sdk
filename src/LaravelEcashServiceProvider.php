<?php

namespace IXCoders\LaravelEcash;

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
            __DIR__.'/../config/laravel-ecash-sdk.php' => config_path('laravel-ecash-sdk.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/create_ecash_transaction_logs_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_ecash_transaction_logs_table.php'),
        ], 'migrations');
    }
}
