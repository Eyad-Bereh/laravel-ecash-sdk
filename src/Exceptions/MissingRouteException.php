<?php

namespace IXCoders\LaravelEcash\Exceptions;

use Exception;

class MissingRouteException extends Exception
{
    public function __construct(string $route)
    {
        $message = "The route ($route) hasn't been configured.";
        $message .= 'Please define this variable for the package to function properly.';
        parent::__construct($message);
    }
}
