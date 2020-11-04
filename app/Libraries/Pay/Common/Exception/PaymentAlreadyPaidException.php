<?php


namespace LaravelSupports\Libraries\Pay\Common\Exception;


class PaymentAlreadyPaidException extends PaymentException
{
    protected $code = 'MS_PY_AL_E3';
    protected $message = '이미 결제가 된 주문 입니다.';
}
