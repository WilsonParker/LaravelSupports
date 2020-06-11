<?php


namespace LaravelSupports\Libraries\Coupon\Exceptions;


class NotEnabledException extends CouponException
{
    protected $code = 'MS_CP_NE_E4';
    protected $message = '해당 쿠폰은 비 활성화 상태로 사용할 수 없습니다';
}
