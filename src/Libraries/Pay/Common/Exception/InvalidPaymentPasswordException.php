<?php


namespace LaravelSupports\Libraries\Pay\Common\Exception;


class InvalidPaymentPasswordException extends PaymentException
{
    protected $code = 'MS_PY_IP_E7';
    protected $message = '카드 비밀번호가 일치하지 않습니다.';
}
