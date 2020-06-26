<?php


namespace LaravelSupports\Libraries\Coupon\Exceptions;


class ExpirationDateException extends CouponException
{
    protected $code = 'MS_CP_ED_E3';
    protected $message = '해당 쿠폰은 유효기간이 만료되어 사용할 수 없습니다.';
}
