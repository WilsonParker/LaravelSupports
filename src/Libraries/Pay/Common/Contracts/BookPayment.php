<?php


namespace LaravelSupports\Libraries\Pay\Common\Contracts;


interface BookPayment extends Payment
{

    public function getPaymentInformation();

    public function updatePaidGoodsStatus($status);
}
