<?php


namespace LaravelSupports\Exceptions\Guzzle\Contracts;


interface ApiRequestBuildable
{
    public function buildRequest($key, $item, $total);
}
