<?php

namespace LaravelSupports\Auth;

use Illuminate\Support\ServiceProvider;
use LaravelSupports\Auth\Contracts\AuthRepositoryContract;

class AuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService(
                $app->make(AuthRepositoryContract::class),
            );
        });
        $this->app->singleton('authService', AuthService::class);
    }
}
