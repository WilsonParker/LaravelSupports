<?php


namespace LaravelSupports\Libraries\Pay\Common\Contracts;


interface Member
{
    public function getID();

    public function getToken();
}
