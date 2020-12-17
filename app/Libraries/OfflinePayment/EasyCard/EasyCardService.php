<?php


namespace LaravelSupports\Libraries\OfflinePayment\EasyCard;


use LaravelSupports\Libraries\OfflinePayment\EasyCard\Request\EasyCardRequest;
use LaravelSupports\Libraries\OfflinePayment\EasyCard\Response\EasyCardResponse;

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

    public function buildResponse(string $data)
    {
        $this->response->bindJson($data);
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
