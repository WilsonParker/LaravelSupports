<?php

namespace LaravelSupports\Exceptions\Loggers;

use LaravelSupports\Exceptions\Contracts\ExceptionRepositoryContract;
use LaravelSupports\Exceptions\ExceptionCodeService;
use LaravelSupports\Exceptions\Loggers\Contracts\Loggable;
use Throwable;

class DatabaseLogger implements Loggable
{
    public function __construct(private ExceptionRepositoryContract $repository) {}

    public function log(Throwable $throwable, array $options = []): void
    {
        $this->repository->store(
            code   : $options['code'] ?? ExceptionCodeService::getCode($throwable),
            message: $options['message'] ?? $throwable->getMessage(),
            url    : $options['url'] ?? request()?->fullUrl(),
            file   : $options['file'] ?? $throwable->getFile(),
            line   : $options['line'] ?? $throwable->getLine(),
            class  : $options['class'] ?? get_class($throwable),
            trace  : $options['trace'] ?? $throwable->getTraceAsString(),
        );
    }
}
