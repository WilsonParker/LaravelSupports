<?php

namespace LaravelSupports\Http;

use Illuminate\Support\ServiceProvider;
use LaravelSupports\Http\Responses\Paginator;
use LaravelSupports\Http\Responses\ResponseTemplate;

class HttpServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ResponseTemplate::class, function ($app) {
            return new ResponseTemplate();
        });
        $this->app->singleton(Paginator::class, function ($app) {
            return new Paginator();
        });

        $this->app->singleton('responseTemplate', ResponseTemplate::class);
        $this->app->singleton('paginator', Paginator::class);
    }
}
