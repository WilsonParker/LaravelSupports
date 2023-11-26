<?php

namespace LaravelSupports\Exceptions;

use Illuminate\Support\ServiceProvider;
use LaravelSupports\Exceptions\Contracts\ExceptionRepositoryContract;
use LaravelSupports\Exceptions\Loggers\Contracts\Loggable;
use LaravelSupports\Exceptions\Loggers\DatabaseLogger;
use LaravelSupports\Exceptions\Repositories\ExceptionRepository;

class ExceptionServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function boot()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
                             __DIR__ . '/../config/exception.php' => config_path('exception.php'),
                         ], 'config');

        if (!class_exists('CreateExceptionsTable')) {
            $this->publishes([
                                 __DIR__ . '/../database/migrations/create_exceptions_table.php.stub' => database_path(
                                     'migrations/' . date('Y_m_d_His', time()) . '_create_exceptions_table.php',
                                 ),
                             ], 'migrations');
        }
    }

    public function register()
    {
        $configPath = __DIR__ . '/../config/exception.php';
        $this->mergeConfigFrom($configPath, 'exception');

        $this->app->singleton(
            ExceptionSlackService::class,
            fn($app) => new ExceptionSlackService($app->make(Loggable::class),),
        );
        
        $this->app->singleton(ExceptionRepository::class, fn($app) => new ExceptionRepository(),);
        $this->app->bind(ExceptionRepositoryContract::class, ExceptionRepository::class);

        $this->app->singleton(ExceptionCodeService::class, fn($app) => new ExceptionCodeService());
        $this->app->singleton(
            DatabaseLogger::class,
            fn($app,) => new DatabaseLogger($app->make(ExceptionRepositoryContract::class)),
        );

        $this->app->bind(
            Loggable::class,
            config('exception.logger'),
        );

        $this->app->singleton(
            ExceptionService::class,
            fn($app) => new ExceptionService(
                $app->make(Loggable::class),
            ),
        );
    }

}
