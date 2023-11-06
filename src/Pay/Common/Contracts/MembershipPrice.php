<?php


namespace LaravelSupports\Pay\Common\Contracts;


interface MembershipPrice extends Price
{
    public function isSubscribe();

    public function getMembershipType();

    public function getDateUnit();

}
