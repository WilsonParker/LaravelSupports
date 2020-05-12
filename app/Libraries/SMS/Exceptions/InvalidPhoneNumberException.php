<?php


namespace LaravelSupports\Libraries\SMS\Exceptions;


use PHPUnit\Framework\Exception;

class InvalidPhoneNumberException extends Exception
{
    protected $message = "Incorrect phone number";

    public function __construct($phone = '')
    {
        $message = $phone != '' ? $phone . " is " . $this->message : $this->message;
        parent::__construct($message);
    }


}
