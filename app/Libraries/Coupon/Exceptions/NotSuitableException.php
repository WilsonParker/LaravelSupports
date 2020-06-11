<?php


namespace LaravelSupports\Libraries\Coupon\Exceptions;


class NotSuitableException extends CouponException
{
    protected $code = 'MS_CP_NS_E6';
    protected $message = '해당 상품에 적합하지 않는 쿠폰 입니다';
}
