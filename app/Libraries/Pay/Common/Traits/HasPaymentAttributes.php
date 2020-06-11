<?php


namespace LaravelSupports\Libraries\Pay\Common\Traits;


use Illuminate\Support\Str;
use LaravelSupports\Libraries\Codes\StringCodeService;

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

    public function setPayload(string $payload)
    {
        $this->{$this->getPayloadName()} = $payload;
    }
}
