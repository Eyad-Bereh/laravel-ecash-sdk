<?php

namespace IXCoders\LaravelEcash\Exceptions;

use Exception;

class InvalidRouteMethodException extends Exception
{
    public function __construct(string $route_name, string $method, array $current_methods)
    {
        $methods = implode(', ', $current_methods);
        $message = "The route defined with the name ($route_name) should support the method ($method), but it currently supports the method(s) ($methods).";
        parent::__construct($message);
    }
}
