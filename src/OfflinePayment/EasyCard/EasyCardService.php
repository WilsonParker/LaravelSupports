<?php


namespace LaravelSupports\OfflinePayment\EasyCard;


use LaravelSupports\OfflinePayment\EasyCard\Request\EasyCardRequest;
use LaravelSupports\OfflinePayment\EasyCard\Response\EasyCardResponse;

class EasyCardService
{
    protected EasyCardRequest $request;
    protected EasyCardResponse $response;

    /**
     * EasyCardService constructor.
     */
    public function __construct()
    {
        $this->request = new EasyCardRequest();
        $this->response = new EasyCardResponse();
    }

    public function buildRequest(array $data)
    {
        $this->request->bindArray($data);
    }

    public function buildResponse(array $data)
    {
        $this->response->bindArray($data, true);
    }

    /**
     * @return EasyCardRequest
     */
    public function getRequest(): EasyCardRequest
    {
        return $this->request;
    }

    /**
     * @return EasyCardResponse
     */
    public function getResponse(): EasyCardResponse
    {
        return $this->response;
    }

}
