<?php


namespace LaravelSupports\Pay\Common\Exception;


class PaymentFailedException extends PaymentException
{
    protected $code = 'MS_PY_FL_E3';
    protected $message = '결제 실패한 주문 입니다.';
}
