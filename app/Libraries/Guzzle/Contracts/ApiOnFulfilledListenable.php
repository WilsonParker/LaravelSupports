<?php


namespace LaravelSupports\Libraries\Guzzle\Contracts;


interface ApiOnFulfilledListenable
{
    public function onFulfilled($response, $index);
}
