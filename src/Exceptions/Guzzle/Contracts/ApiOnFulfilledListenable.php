<?php


namespace LaravelSupports\Exceptions\Guzzle\Contracts;


interface ApiOnFulfilledListenable
{
    public function onFulfilled($response, $index);
}
