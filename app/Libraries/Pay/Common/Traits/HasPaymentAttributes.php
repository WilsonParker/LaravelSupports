<?php


namespace LaravelSupports\Libraries\Pay\Common\Traits;



use App\Services\Codes\StringCodeService;

trait HasPaymentAttributes
{

    public function getPayload()
    {
        $service = new StringCodeService(64);
        return isset($this->{$this->getPayloadName()}) ? $this->{$this->getPayloadName()} : $service->createCode();
    }

    public function getPayloadName()
    {
        return 'payload';
    }

    public function setPayload($payload)
    {
        $this->{$this->getPayloadName()} = $payload;
    }
}
