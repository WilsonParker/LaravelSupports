<?php

namespace LaravelSupports\Exceptions;

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionCodeService
{
    public function getCode(Throwable $throwable): int
    {
        $code = $throwable->getCode();
        if ($throwable instanceof ValidationException) {
            $code = ResponseAlias::HTTP_FORBIDDEN;
        } else if ($throwable instanceof NotFoundHttpException) {
            $code = ResponseAlias::HTTP_NOT_FOUND;
        } else if ($this->isInvalidCode($throwable->getCode())) {
            $code = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR;
        }

        return $code;
    }

    public function isInvalidCode($code): bool
    {
        $code = (int)$code;
        return $code < 100 || $code >= 600;
    }
}
