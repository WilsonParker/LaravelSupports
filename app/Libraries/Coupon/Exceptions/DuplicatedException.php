<?php


namespace LaravelSupports\Libraries\Coupon\Exceptions;


class DuplicatedException extends CouponException
{
    protected $code = 'MS_CP_DP_E10';
    protected $message = '이미 다른 쿠폰을 등록하셨습니다.';
}
