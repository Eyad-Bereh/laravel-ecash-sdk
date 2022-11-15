<?php

namespace IXCoders\LaravelEcash\Exceptions;

use Exception;

class EcashTransactionSaveFailedException extends Exception
{
    public function __construct()
    {
        $message = "The transaction couldn't be saved inside the transactions table.";
        parent::__construct($message);
    }
}
