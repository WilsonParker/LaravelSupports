<?php


namespace LaravelSupports\Libraries\Coupon\Contracts;


interface Coupon
{
    public function getUniqueName();

    public function getUniqueValue();

    public function getCouponTypeCode();

    public function getCouponValue();

    public function getCouponUsableCount();

}
