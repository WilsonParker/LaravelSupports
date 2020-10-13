<?php


namespace LaravelSupports\Libraries\Push\Exceptions;


use Exception;

class HasPushTokenException extends Exception
{
    protected $message = '$tokenModelClass must implements HasPushToken interface';
}
