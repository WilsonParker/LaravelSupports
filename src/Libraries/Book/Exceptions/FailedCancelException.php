<?php


namespace LaravelSupports\Libraries\Book\Exceptions;


class FailedCancelException extends BookException
{
    protected $code = 'BK_CC_FL_E1';
    protected $message = '취소 불가한 주문입니다';
}
