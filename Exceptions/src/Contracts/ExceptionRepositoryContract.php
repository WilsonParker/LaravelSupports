<?php

namespace LaravelSupports\Exceptions\Contracts;

use LaravelSupports\Exceptions\Models\ExceptionModel;

interface ExceptionRepositoryContract
{
    public function store(
        int    $code,
        string $message,
        string $url,
        string $file,
        string $line,
        string $class,
        string $trace,
    ): ExceptionModel;
}