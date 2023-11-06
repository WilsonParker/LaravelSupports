<?php


namespace LaravelSupports\Push\Contracts;


interface HasPushToken
{
    public function getTokenName(): string;
}
