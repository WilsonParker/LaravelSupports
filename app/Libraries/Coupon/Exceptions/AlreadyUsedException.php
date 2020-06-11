<?php


namespace LaravelSupports\Libraries\Coupon\Exceptions;


class AlreadyUsedException extends CouponException
{
    protected $code = 'MS_CP_AU_E2';
    protected $message = '이미 사용한 쿠폰 입니다';
}
