<?php

namespace Gottvergessen\Logger\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Gottvergessen\Logger\Logger
 */
class Logger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Gottvergessen\Logger\Logger::class;
    }
}
