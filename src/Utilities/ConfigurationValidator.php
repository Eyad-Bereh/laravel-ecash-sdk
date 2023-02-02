<?php

namespace IXCoders\LaravelEcash\Utilities;

use Illuminate\Support\Str;

class ConfigurationValidator
{
    public static function isValidCheckoutType(string $value): bool
    {
        $value = Str::upper($value);
        $checkout_types = config('laravel-ecash-sdk.checkout_types');

        return in_array($value, $checkout_types);
    }

    public static function isValidCurrency(string $value): bool
    {
        $value = Str::upper($value);
        $currencies = config('laravel-ecash-sdk.currencies');

        return in_array($value, $currencies);
    }

    public static function checkIfConfigurationValueIsSet(string $key): bool
    {
        $option = 'laravel-ecash-sdk.' . $key;
        $value = config($option);

        return !is_null($value);
    }
}
