<?php


namespace LaravelSupports\Pay\Common\Exception;


class IsNotReturnablePaymentException extends PaymentException
{
    protected $code = 'PY_RT_EX_E1';
    protected $message = '반납 불가한 주문 입니다.';
}
