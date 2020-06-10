<?php


namespace LaravelSupports\Libraries\Coupon\Contracts\Prices;


interface Price
{
    public function getCode();

    public function getNumber();

    public function getPrice();

    public function isSubscribe();

    public function setSalePrice();

}
