<?php

namespace IXCoders\LaravelEcash\Exceptions;

use Exception;

class MissingMiddlewareException extends Exception
{
    public function __construct(string $route, string $middleware, ?string $alternative = null)
    {
        $message = "The route ($route) should use the middleware ($middleware).";
        if (! is_null($alternative)) {
            $message .= "This middleware is named as ($alternative).";
        }
        parent::__construct($message);
    }
}
