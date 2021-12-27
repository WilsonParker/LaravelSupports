<?php

namespace LaravelSupports\Libraries\Exceptions\Contracts;

use Exception;

/**
 * Exception 에 따라 Handler 에서 처리할 수 있도록 공통 함수를 정의합니다
 *
 * @author  WilsonParker
 * @class   ExceptionHandleable.php
 * @added   2019.03.05
 * @updated 2019.03.05
 */
interface ExceptionHandleable
{
    /**
     * Exception 을 처리합니다
     *
     * @param   Exception $exception
     * @return
     * @author  WilsonParker
     * @added   2019.03.05
     * @updated 2019.03.05
     * @bug
     * @see
     */
    public function handle($exception);
}
