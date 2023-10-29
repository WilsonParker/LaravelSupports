<?php

namespace LaravelSupports\Libraries\Exceptions\Handlers;

use LaravelSupports\Libraries\Exceptions\Contracts\ExceptionHandleable;

class InvalidExceptionHandler implements ExceptionHandleable
{
    public function handle($exception){
        echo "handle";
    }
}
