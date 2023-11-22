<?php

namespace LaravelSupports\Exceptions\Handlers;

use LaravelSupports\Exceptions\Contracts\ExceptionHandleable;

class InvalidExceptionHandler implements ExceptionHandleable
{
    public function handle($exception)
    {
        echo "handle";
    }
}
