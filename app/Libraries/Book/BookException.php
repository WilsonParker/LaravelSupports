<?php


namespace LaravelSupports\Libraries\Book;


class BookException extends \Exception
{
    protected $code = ' BK_EX_E1';
    protected $message = '책 정보가 유효하지 않습니다.';
}
