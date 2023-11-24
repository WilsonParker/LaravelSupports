<?php


namespace LaravelSupports\Coupon\Contracts;


interface CouponCondition extends Coupon
{
    public function getCouponValueRegex();
}
