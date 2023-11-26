<?php

namespace LaravelSupports\Exceptions\Facades;

use Illuminate\Support\Facades\Facade;

class ExceptionCodeService extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return \LaravelSupports\Exceptions\ExceptionCodeService::class; }
}
