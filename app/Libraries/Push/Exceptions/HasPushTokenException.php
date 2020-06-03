<?php


namespace LaravelSupports\Libraries\Push\Exceptions;


class HasPushTokenException extends \Exception
{
    protected $message = '$tokenModelClass must implements HasPushToken interface';
}
