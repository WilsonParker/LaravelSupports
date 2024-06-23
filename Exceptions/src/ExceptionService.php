<?php

namespace LaravelSupports\Exceptions;

use LaravelSupports\Exceptions\Loggers\Contracts\ExceptionServiceContract;
use LaravelSupports\Exceptions\Loggers\Contracts\Loggable;
use Throwable;

class ExceptionService implements ExceptionServiceContract
{

    public function __construct(protected Loggable $logger) {}

    public function log(Throwable $throwable, array $options = []): void
    {
        $this->logger->log($throwable);
    }

}
