<?php


namespace LaravelSupports\Coupon\Exceptions;


class NotFoundException extends CouponException
{
    protected $code = 'MS_CP_NF_E8';
    protected $message = '해당 코드에 대한 쿠폰이 존재하지 않습니다.';
}
