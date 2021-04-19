<?php


namespace LaravelSupports\Libraries\Book;


class GoodsException extends BookException
{
    protected $code = 'GD_EX_E1';
    protected $message = '구매 하시려는 도서 중 구매 불가한 도서가 있습니다.';
}
