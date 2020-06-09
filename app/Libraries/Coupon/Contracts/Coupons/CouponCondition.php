<?php


namespace LaravelSupports\Libraries\Coupon\Contracts\Coupons;


interface CouponCondition extends Coupon
{
    public function getCouponValueRegex();
}
