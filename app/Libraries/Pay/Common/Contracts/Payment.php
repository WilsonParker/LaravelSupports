<?php


namespace LaravelSupports\Libraries\Pay\Common\Contracts;


interface Payment
{
    const STATUS_READY = 'ready';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FAILED = 'failed';

    public function getID();

    public function getName();

    public function getPayAmount();

    public function getDeliveryCost();

    public function getPayload();

    public function getPayloadName();

    public function isSubscribe();

    public function isReady();

    public function isPaid();

    public function isCancelled();

    public function isFailed();

    public function getType();

    public function getQuantity();

    public function getToken();

    public function setToken($token);

    public function getStatus();

    public function setStatus($status);

    public function getAID();

    public function setAID($id);

    public function getTID();

    public function setTID($id);

    public function getCID();

    public function setCID($id);

    public function getUID();

    public function setUID($id);

    public function getSID();

    public function setSID($id);

    public function getPaymentType();

    public function setPaymentType($type);

    public function getPgTid();

    public function setPgTid($id);

    public function getPgProvider();

    public function setPgProvider($provider);

    public function getDescription();

    public function setDescription($description);
}
