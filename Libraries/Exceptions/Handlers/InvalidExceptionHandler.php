<?php

namespace LaravelSupports\Libraries\Exceptions\Handlers;

use App\Exceptions\Contracts\ExceptionHandleable;
use \Exception;

class InvalidExceptionHandler implements ExceptionHandleable
{
    public function handle($exception){
        echo "handle";
    }
}
