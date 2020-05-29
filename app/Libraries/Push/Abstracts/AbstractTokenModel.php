<?php


namespace LaravelSupports\LibrariesPush\Abstracts;


use LaravelSupports\LibrariesPush\Contracts\HasPushToken;

abstract class AbstractTokenModel implements HasPushToken
{
    public function getTokenName(): string
    {
        return 'token';
    }
}
