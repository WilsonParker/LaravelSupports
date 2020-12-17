<?php


namespace LaravelSupports\Libraries\OfflinePayment\EasyCard\Exception;


class PaymentFailException extends \Exception
{
    protected $code = 'OF_PY_FL';
    protected $message = '결제가 실패 되었습니다.';

}
