<?php


namespace LaravelSupports\LibrariesPush\Exceptions;


class HasPushTokenException extends \Exception
{
    protected $message = '$tokenModelClass must implements HasPushToken interface';
}
