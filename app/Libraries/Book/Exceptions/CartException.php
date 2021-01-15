<?php


namespace LaravelSupports\Libraries\Book\Exceptions;


class CartException extends \Exception
{
    protected $code = 'BK_CT_EX_E1';
    protected $message = '장바구니에 오류가 발생했습니다.';
}
