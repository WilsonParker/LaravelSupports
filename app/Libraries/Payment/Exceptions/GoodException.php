<?php


namespace LaravelSupports\Libraries\Payment\Exceptions;


class GoodException extends \Exception
{
    protected $code = 'GD_EX_E1';
    protected $message = '유효하지 않은 상품입니다.';
}
