<?php


namespace LaravelSupports\Coupon\Contracts;


interface Coupon
{
    public function getUniqueName();

    public function getUniqueValue();

    public function getCouponTypeCode();

    public function getCouponValue();

    public function getCouponBenefitCount();

    public function getCouponSubValue();

    public function getDescription();
}
