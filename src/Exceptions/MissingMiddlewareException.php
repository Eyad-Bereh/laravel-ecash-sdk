<?php

namespace IXCoders\LaravelEcash\Exceptions;

use Exception;

class MissingMiddlewareException extends Exception
{
    public function __construct(string $route, string $middleware, ?string $alias = NULL)
    {
        $message = "The route ($route) should use the middleware ($middleware).";
        if (!is_null($alias)) {
            $message .= "This middleware is named as ($alias).";
        }
        parent::__construct($message);
    }
}
