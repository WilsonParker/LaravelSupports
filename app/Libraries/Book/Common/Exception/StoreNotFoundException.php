<?php


namespace App\Services\Book\Common\Exception;


class StoreNotFoundException
{
    protected $code = 'Bk_ST_EX_E1';
    protected $message = '존재하지 않는 업체입니다.';
}