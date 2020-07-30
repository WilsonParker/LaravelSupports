<?php


namespace LaravelSupports\Libraries\Guzzle\Contracts;


interface ApiRequestBuildable
{
    public function buildRequest($key, $item, $total);
}
