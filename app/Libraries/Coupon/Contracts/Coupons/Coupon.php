<?php


namespace LaravelSupports\Libraries\Coupon\Contracts\Coupons;


interface Coupon
{
    public function getCouponTypeCode();

    public function getCouponValue();

}
