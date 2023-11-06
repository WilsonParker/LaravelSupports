<?php


namespace LaravelSupports\Pay\Common\Contracts;


interface BookPayment extends Payment
{

    public function getPaymentInformation();

    public function updatePaidGoodsStatus($status);
}
