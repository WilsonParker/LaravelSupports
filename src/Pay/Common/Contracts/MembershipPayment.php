<?php


namespace LaravelSupports\Pay\Common\Contracts;


interface MembershipPayment extends Payment
{

    public function getMemberModel();

    public function getPriceModel();

    public function getCouponModel();

    public function getMembershipType();

    public function isPointProvidable();

}
