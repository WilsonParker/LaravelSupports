<?php


namespace LaravelSupports\Coupon\Exceptions;


class EarlierThanAvailableDateException extends CouponException
{
    protected $code = 'MS_CP_EA_E9';
    protected $message = '해당 쿠폰을 사용할 수 있는 날짜가 지난 후 사용해주세요.';
}
