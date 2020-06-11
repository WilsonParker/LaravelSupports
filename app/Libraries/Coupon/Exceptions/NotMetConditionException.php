<?php


namespace LaravelSupports\Libraries\Coupon\Exceptions;


class NotMetConditionException extends CouponException
{
    protected $code = 'MS_CP_NM_E5';
    protected $message = '조건 미달로 인하여 해당 쿠폰을 사용할 수 없습니다';
}
