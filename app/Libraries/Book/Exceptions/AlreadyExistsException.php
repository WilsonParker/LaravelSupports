<?php


namespace LaravelSupports\Libraries\Book\Exceptions;


class AlreadyExistsException extends CartException
{
    protected $code = 'CT_AE_EX_E1';
    protected $message = "이미 담은 책이에요";
}
