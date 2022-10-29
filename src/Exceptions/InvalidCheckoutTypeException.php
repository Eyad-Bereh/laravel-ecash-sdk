<?php

namespace IXCoders\LaravelEcash\Exceptions;

use Exception;

class InvalidCheckoutTypeException extends Exception
{
    public function __construct(string $value)
    {
        $checkout_types = config('laravel-ecash-sdk.checkout_types');
        $checkout_types = '['.implode(', ', $checkout_types).']';
        $message = "Unknown checkout type has been used. Expected one of $checkout_types but instead got ($value).";
        parent::__construct($message);
    }
}
