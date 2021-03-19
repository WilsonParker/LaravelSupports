<?php


namespace LaravelSupports\Libraries\Payment\Exceptions;


class GoodStatusException extends GoodException
{
    protected $code = 'GD_ST_EX_E1';
    protected $message = '변경할 수 없는 상태 입니다.';
}
