<?php


namespace LaravelSupports\Libraries\Push\Abstracts;


use LaravelSupports\Libraries\Push\Contracts\HasPushToken;

abstract class AbstractTokenModel implements HasPushToken
{
    public function getTokenName(): string
    {
        return 'token';
    }
}
