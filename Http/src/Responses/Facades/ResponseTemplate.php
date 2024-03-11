<?php

namespace LaravelSupports\Http\Responses\Facades;

use Illuminate\Support\Facades\Facade;
use Throwable;

class ResponseTemplate extends Facade implements Throwable
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'responseTemplate';
    }
}
