<?php


namespace LaravelSupports\Libraries\Book\Exceptions;


class OverQuantityException extends CartException
{
    protected $code = 'OQ_EX_E1';
    protected $message = '장바구니에 담을 수 있는 수량을 초과하였습니다.';
}
