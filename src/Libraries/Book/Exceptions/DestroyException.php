<?php


namespace LaravelSupports\Libraries\Book\Exceptions;


class DestroyException extends CartException
{
    protected $code = 'CT_DS_EX_E1';
    protected $message = '장바구니 삭제하는데 오류가 발생했습니다.';
}
