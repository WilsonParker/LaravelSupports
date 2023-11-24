<?php


namespace LaravelSupports\Pay\Common\Exception;


use Exception;

class PaymentException extends Exception
{
    protected $code = 'MS_PY_EX_E1';
    protected $message = '결제를 실패하였습니다.';
}
