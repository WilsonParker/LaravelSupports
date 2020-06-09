<?php


namespace LaravelSupports\Libraries\Coupon\Contracts\Prices;


interface Price
{
    public function getPrice();

    public function isSubscribe();

    public function setSalePrice();

}
