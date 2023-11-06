<?php


namespace LaravelSupports\Pay\Common\Contracts;


interface Price
{
    public function getID();

    public function getNumber();

    public function getName();

    public function getPrice();

    public function isSubscribe();

}
