<?php

namespace Gottvergessen\Logger;

use Illuminate\Support\Str;

class Logger {
    public static function batch(callable $callback): mixed
    {
        app()->instance('logger.batch', (string) Str::uuid());

        try {
            return $callback();
        } finally {
            app()->forgetInstance('logger.batch');
        }
    }
}
