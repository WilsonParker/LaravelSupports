<?php


namespace LaravelSupports\Libraries\Pay\Common\Contracts;


interface Payment
{
    public function getID();

    public function getName();

    public function getPayAmount();

    public function getPayload();

    public function getPayloadName();

}
