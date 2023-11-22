<?php

namespace LaravelSupports\Locale;

use Illuminate\Support\ServiceProvider;
use LaravelSupports\Locale\Contracts\LocaleRepositoryContract;
use LaravelSupports\Locale\Contracts\LocaleServiceContract;
use LaravelSupports\Locale\Repository\LocaleRepository;

class LocaleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(LocaleRepository::class, function ($app) {
            return new LocaleRepository();
        });
        $this->app->bind(LocaleRepositoryContract::class, LocaleRepository::class);

        $this->app->singleton(LocaleService::class, function ($app) {
            return new LocaleService($app->make(LocaleRepositoryContract::class));
        });
        $this->app->bind(LocaleServiceContract::class, LocaleService::class);
    }

}
