<?php


namespace LaravelSupports\Coupon\Contracts;


interface CouponBenefit extends Coupon
{
    public function getCouponValueRegex();

    public function getCouponSubValueRegex();

    public function getCouponThirdValue();
}
