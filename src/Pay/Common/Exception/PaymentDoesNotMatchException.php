<?php


namespace LaravelSupports\Pay\Common\Exception;


class PaymentDoesNotMatchException extends PaymentException
{
    protected $code = 'MS_PY_NM_E5';
    protected $message = '현재 이용 중인 멤버십 정보와 결제 하려는 멤버십 정보가 일치하지 않습니다.';
}
