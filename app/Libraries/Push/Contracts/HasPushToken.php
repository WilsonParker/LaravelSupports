<?php


namespace LaravelSupports\Libraries\Push\Contracts;


interface HasPushToken
{
    public function getTokenName(): string;
}
