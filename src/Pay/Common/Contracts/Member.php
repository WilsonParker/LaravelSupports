<?php


namespace LaravelSupports\Pay\Common\Contracts;


interface Member
{
    public function getID();

    public function getToken();
}
