<?php


namespace LaravelSupports\Libraries\Book\Exceptions;


class LocationException extends \Exception
{
    protected $code = 'LO_EX_E1';
    protected $message = '현재 지원되지 않는 지역입니다.';
}
