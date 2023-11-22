<?php


namespace LaravelSupports\Push\Exceptions;


use Exception;

class HasPushTokenException extends Exception
{
    protected $message = '$tokenModelClass must implements HasPushToken interface';
}
