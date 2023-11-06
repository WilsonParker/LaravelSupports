<?php


namespace LaravelSupports\Coupon\Exceptions;


use Exception;

class CouponException extends Exception
{
    protected $code = 'MS_CP_NU_E1';
    protected $message = '사용할 수 없는 쿠폰 입니다.';
}
