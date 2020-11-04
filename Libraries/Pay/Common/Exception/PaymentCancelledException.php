<?php


namespace LaravelSupports\Libraries\Pay\Common\Exception;


class PaymentCancelledException extends PaymentException
{
    protected $code = 'MS_PY_CC_E2';
    protected $message = '결제 취소된 주문 입니다.';
}
