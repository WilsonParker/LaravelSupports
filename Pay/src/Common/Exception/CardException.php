<?php


namespace LaravelSupports\Pay\Common\Exception;


class CardException extends PaymentException
{
    protected $code = 'CD_EX_E1';
    protected $message = '카드 정보가 유효하지 않습니다.';
}
