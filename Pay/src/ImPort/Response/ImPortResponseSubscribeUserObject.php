<?php


namespace LaravelSupports\Pay\ImPort\Response;


use Exception;
use LaravelSupports\Pay\Common\Exception\PaymentException;

class ImPortResponseSubscribeUserObject extends ImPortResponse
{
    /**
     * @inheritDoc
     */
    public function bindStd($std)
    {
        try {
            parent::bindStd($std);
        } catch (Exception $e) {
            throw new PaymentException("저장된 카드 정보를 불러올 수 없습니다.\n 수동으로 카드 정보를 입력하여 결제 부탁드립니다");
        }
    }

}
