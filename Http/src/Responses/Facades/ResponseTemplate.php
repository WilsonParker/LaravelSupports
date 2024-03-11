<?php

namespace LaravelSupports\Http\Responses\Facades;

use Illuminate\Support\Facades\Facade;

class ResponseTemplate extends Facade
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
