<?php


namespace LaravelSupports\Libraries\Coupon\Contracts;


interface CouponBenefit extends Coupon
{
    public function getCouponValueRegex();

    public function getCouponSubValueRegex();

    public function getCouponSubValue();

    public function getCouponThirdValue();
}
