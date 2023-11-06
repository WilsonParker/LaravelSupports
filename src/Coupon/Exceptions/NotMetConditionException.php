<?php


namespace LaravelSupports\Coupon\Exceptions;


class NotMetConditionException extends CouponException
{
    protected $code = 'MS_CP_NM_E5';
    protected $message = '사용 조건이 적합하지 않아 해당 쿠폰을 사용할 수 없습니다.';
}
