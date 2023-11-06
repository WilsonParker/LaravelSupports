<?php


namespace LaravelSupports\Coupon\Exceptions;


class UsageCountExceededException extends CouponException
{
    protected $code = 'MS_CP_CE_E7';
    protected $message = '해당 쿠폰을 사용할 수 있는 횟수가 초과 되었습니다.';
}
