<?php

namespace IXCoders\LaravelEcash;

use Illuminate\Support\Facades\Facade;

/**
 * @see \IXCoders\LaravelEcash\LaravelEcash
 */
class LaravelEcash extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ecash.laravel';
    }
}
