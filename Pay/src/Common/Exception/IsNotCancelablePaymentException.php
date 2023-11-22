<?php


namespace LaravelSupports\Pay\Common\Exception;


class IsNotCancelablePaymentException extends PaymentException
{
    protected $code = 'PY_CC_EX_E1';
    protected $message = '취소 불가한 주문 입니다.';
}
