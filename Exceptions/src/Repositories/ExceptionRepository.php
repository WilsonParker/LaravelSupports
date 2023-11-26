<?php

namespace LaravelSupports\Exceptions\Repositories;

use LaravelSupports\Exceptions\Contracts\ExceptionRepositoryContract;
use LaravelSupports\Exceptions\Models\ExceptionModel;

class ExceptionRepository implements ExceptionRepositoryContract
{
    public function store(
        int    $code,
        string $message,
        string $url,
        string $file,
        string $line,
        string $class,
        string $trace,
    ): ExceptionModel {
        return ExceptionModel::create(
            [
                'code' => $code,
                'message' => $message,
                'url' => $url,
                'file' => $file,
                'line' => $line,
                'class' => $class,
                'trace' => $trace,
            ]);
    }
}