<?php

namespace IXCoders\LaravelEcash\Exceptions;

use Exception;

class MissingRouteException extends Exception
{
    public function __construct(string $route, string $alternative)
    {
        $message = "Neither the route ($route), nor the environment variable ($alternative) has been configured.";
        $message .= "Please define one of these variables for the package to function properly";
        parent::__construct($message);
    }
}
