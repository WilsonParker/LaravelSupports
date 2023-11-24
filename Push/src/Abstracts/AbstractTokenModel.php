<?php


namespace LaravelSupports\Push\Abstracts;


use LaravelSupports\Push\Contracts\HasPushToken;

abstract class AbstractTokenModel implements HasPushToken
{
    public function getTokenName(): string
    {
        return 'token';
    }
}
