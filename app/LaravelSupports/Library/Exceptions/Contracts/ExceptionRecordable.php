<?php

namespace App\LaravelSupports\Library\Exceptions\Contracts;

/**
 * Exception 발생 시 기록할 방식을 제공하도록 하는 interface 입니다
 *
 * @author  WilsonParker
 * @class   ExceptionRecordable.php
 * @added   2019.03.04
 * @updated 2019.03.04
 * @bug
 * @todo
 * @see
 */
interface ExceptionRecordable
{
    /**
     * Exception 을 기록합니다
     *
     * @param   \Exception $exception
     * @return  Void|Mixed
     * @author  WilsonParker
     * @added   2019.03.04
     * @updated 2019.03.04
     * @bug
     * @see
     */
    public function record($exception);
}
