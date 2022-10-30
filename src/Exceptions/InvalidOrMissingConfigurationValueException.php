<?php

namespace IXCoders\LaravelEcash\Exceptions;

use Exception;

class InvalidOrMissingConfigurationValueException extends Exception
{
    public function __construct(string $key)
    {
        $message = "Invalid or missing configuration value for the key ($key).";
        $message .= "Please check the values set inside the configuration file (laravel-ecash-sdk.php) for any invalid or missing options and try again.";
        parent::__construct($message);
    }
}
