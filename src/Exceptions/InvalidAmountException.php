<?php

namespace IXCoders\LaravelEcash\Exceptions;

use Exception;

class InvalidAmountException extends Exception
{
    public function __construct(int $amount)
    {
        $message = "Invalid amount ($amount) has been used.";
        parent::__construct($message);
    }
}
