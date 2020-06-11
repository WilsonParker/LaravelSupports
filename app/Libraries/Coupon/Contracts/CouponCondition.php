<?php


namespace LaravelSupports\Libraries\Coupon\Contracts;


interface CouponCondition extends Coupon
{
    public function getCouponValueRegex();
}
