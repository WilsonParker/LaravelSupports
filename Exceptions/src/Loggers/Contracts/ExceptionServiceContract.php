<?php

namespace LaravelSupports\Exceptions\Loggers\Contracts;

use Throwable;

interface ExceptionServiceContract
{
    public function log(Throwable $throwable, array $options = []): void;

}
