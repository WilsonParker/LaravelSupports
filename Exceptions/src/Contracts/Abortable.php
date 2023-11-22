<?php

namespace LaravelSupports\Exceptions\Contracts;

/**
 * abort 를 실행 가능한 interface
 * @author  dev9163
 * @added   2021/11/18
 * @updated 2021/11/18
 */
interface Abortable
{
    public function getCode();
}
