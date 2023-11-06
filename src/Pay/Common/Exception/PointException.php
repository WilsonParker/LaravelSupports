<?php


namespace LaravelSupports\Pay\Common\Exception;


class PointException extends PaymentException
{
    protected $code = 'PT_EX_E1';
    protected $message = '사용 가능 포인트를 초과합니다.';
}
