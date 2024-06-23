<?php

namespace LaravelSupports\Exceptions\Loggers\Contracts;

use Throwable;

interface Loggable
{
    public function log(Throwable $throwable, array $options = []): void;

}
