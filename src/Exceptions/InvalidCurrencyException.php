<?php

namespace IXCoders\LaravelEcash\Exceptions;

use Exception;

class InvalidCurrencyException extends Exception
{
    public function __construct(string $currency)
    {
        $currencies = config('laravel-ecash-sdk.currencies');
        $currencies = '['.implode(', ', $currencies).']';
        $message = "Unknown currency has been used. Expected one of $currencies but instead got ($currency).";
        parent::__construct($message);
    }
}
