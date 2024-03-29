<?php

namespace LaravelSupports\Exceptions\Logs;

use Exception;
use LaravelSupports\Exceptions\Contracts\ExceptionRecordable;
use LaravelSupports\Objects\ConstructOverrideObject;


/**
 * File 에 Exception Log 를 기록합니다
 *
 * @author  WilsonParker
 * @class   ExceptionLogFile.php
 * @added   2019.03.04
 * @updated 2019.03.04
 */
class ExceptionLogFile extends ConstructOverrideObject implements ExceptionRecordable
{

    /**
     * Exception 을 이용하여 File 에 기록합니다
     *
     * @param Exception $exception
     * @return
     * @author  WilsonParker
     * @added   2019.03.04
     * @updated 2019.03.04
     * @bug
     * @see
     */
    public function record($exception)
    {

    }

}
