<?php


namespace LaravelSupports\Libraries\Coupon\Contracts\Coupons;


interface CouponBenefit extends Coupon
{
    public function getCouponValueRegex();

    public function getCouponSubValueRegex();

    public function getCouponSubValue();
}
