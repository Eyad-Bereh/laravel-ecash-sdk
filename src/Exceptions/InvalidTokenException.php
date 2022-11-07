<?php

namespace IXCoders\LaravelEcash\Exceptions;

use Exception;

class InvalidTokenException extends Exception
{
    public function __construct(string $token)
    {
        $message = "Cannot proceed because of invalid verification token supplied ($token).";
        parent::__construct($message);
    }
}
