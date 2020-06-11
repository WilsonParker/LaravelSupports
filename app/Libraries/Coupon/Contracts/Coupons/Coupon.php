<?php


namespace LaravelSupports\Libraries\Coupon\Contracts\Coupons;


interface Coupon
{
    public function getUniqueName();

    public function getUniqueValue();

    public function getCouponTypeCode();

    public function getCouponValue();

}
