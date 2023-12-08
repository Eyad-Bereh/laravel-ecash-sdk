# WARNING: DUE TO LACK OF TIME AND TECHNICAL CAPABILITIES AND INSUFFICIENT DOCUMENTATION FROM THE PAYMENT GATEWAY AND HAVING TO PAY ATTENTION TO MY JOB, I NO LONGER WILL BE ABLE TO MAINTAIN THIS PACKAGE, AND STARTING FROM 2023-12-08 THIS REPOSITORY IS ARCHIVED AND THE PACKAGE ON PACKAGIST.ORG WILL NO LONGER BE AVAILABLE FOR DOWNLOAD. CONSIDER THE DOCUMENTATION IN THIS REPOSITORY DEPRECATED AND IT SHOULD BE ONLY USED FOR LEGACY CODE AND HISTORICAL PURPOSES. IF YOU HAVE THE TIME, PLEASE FORK THIS REPOSITORY AND WORK YOUR CHANGES.

# Unofficial Laravel SDK for E-cash

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ixcoders/laravel-ecash-sdk.svg?style=flat-square)](https://packagist.org/packages/ixcoders/laravel-ecash-sdk)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/ixcoders/laravel-ecash-sdk/run-tests?label=tests)](https://github.com/ixcoders/laravel-ecash-sdk/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/ixcoders/laravel-ecash-sdk/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/ixcoders/laravel-ecash-sdk/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ixcoders/laravel-ecash-sdk.svg?style=flat-square)](https://packagist.org/packages/ixcoders/laravel-ecash-sdk)

A basic and simple package that aims to simplify the goal of integrating Ecash payment system into Laravel.

## Installation

You can install the package via composer:

```bash
composer require ixcoders/laravel-ecash-sdk
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="IXCoders\\LaravelEcash\\LaravelEcashServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
// config for IXCoders/LaravelEcash

return [
    /**
     * Checkout types
     *
     * Defines the available checkout types for the system.
     */
    "checkout_types" => [
        "CARD",
        "QR"
    ],

    /**
     * Terminal key
     *
     * Defines the terminal key which will be used.
     */
    "terminal_key" => env("ECASH_TERMINAL_KEY"),

    /**
     * Merchant ID
     *
     * Defines the Merchant ID of the account that will be used.
     */
    "merchant_id"   =>  env("ECASH_MERCHANT_ID"),

    /**
     * Merchant secret
     *
     * Defines the secret key that links to the merchant account.
     */
    "merchant_secret" => env("ECASH_MERCHANT_SECRET"),

    /**
     * Currencies
     *
     * Defines the currencies that are currently supported by the system.
     */
    "currencies" => ["SYP"],

    /**
     * Redirect route
     *
     * Defines the name of the route which the user will be redirected to after the operation ends.
     */
    "redirect_route" => "ecash.redirect",

    /**
     * Callback route
     *
     * Defines the name of the route which will be called upon the operation finish.
     */
    "callback_route" => "ecash.callback"
];

```

## Configuration

This package requires from you to setup a number of parameters and confguration values before proceeding with its usage and they all can be found in the configuration file `laravel-ecash-sdk.php`, let's take a look at them:

-   `terminal_key` — Defines the terminal key which will be used. This value can be configured by setting the environment variable `ECASH_TERMINAL_KEY`.
-   `merchant_id` — Defines the Merchant ID of the account that will be used. This value can be configured by setting the environment variable `ECASH_MERCHANT_ID`.
-   `merchant_secret` — Defines the secret key that links to the merchant account. This value can be configured by setting the environment variable `ECASH_MERCHANT_SECRET`.
-   `redirect_route` — Defines the name of the route which the user will be redirected to after the operation ends. You can either define a route named `ecash.redirect` in your routes file, or you can set the environment variable `ECASH_REDIRECT_URL`.
-   `callback_route` — Defines the name of the route which will be called upon the operation finish. You can either define a route named `ecash.callback` in your routes file, or you can set the environment variable `ECASH_CALLBACK_URL`.

**Note:** The package will first look for the named routes and check if they're defined, if yes then it will use them, if no then it will look for the environment variables, if they're defined it will use them, if not then it will throw a `MissingRouteException` exception. Just remember that named routes has a higher priority.

## Usage

There are many ways you can use the package, let's take a look at each one.

-   Use the `LaravelEcash` facade:

```php
use IXCoders\LaravelEcash\Facades\LaravelEcash;

$verificationCode = LaravelEcash::getVerificationCode($amount, $reference);
```

-   Obtain an instance out of the service container:

```php

$object = app("ecash.laravel");
$verificationCode = $object->getVerificationCode($amount, $reference);

```

-   Create an instance of `IXCoders\LaravelEcash\LaravelEcash` manually (not recommended):

```php

$object = new IXCoders\LaravelEcash\LaravelEcash();
$verificationCode = $object->getVerificationCode($amount, $reference);

```

## Available functionality

The core of the package is the class `IXCoders\LaravelEcash\LaravelEcash`, and it defines the following methods for public usage:

```php
class LaravelEcash {

    public function getVerificationCode(int $amount, string $reference): string;

    public function checkVerificationCode(string $hash, int $amount, string $reference): bool

    public function generatePaymentLink(string $checkout_type, int $amount, string $reference, string $currency = "SYP", ?string $language = NULL): string;

}
```

Most of the time, you will use the `generatePaymentLink()` method.

This package can also throw a number of exceptions in unfortunate circumstances, and they all lies under the namespace `IXCoders\LaravelEcash\Exceptions`:

-   `InvalidAmountException` — Thrown upon the insertion of incorrect amount to be processed.
-   `InvalidCheckoutTypeException` — Thrown if an invalid checkout type has been passed.
-   `InvalidCurrencyException` — Thrown if an invalid currency has been passed.
-   `InvalidOrMissingConfigurationValueException` — Thrown if the configuration file contains invalid or missing configuration values that are necessary for the package to function correctly.
-   `MissingRouteException` — Thrown if a route couldn't be found for redirect and callback. Please check the configuration section on routes for further information.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Eyad Bereh](https://github.com/Eyad-Mohammed-Osama)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
