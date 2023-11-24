<?php


namespace LaravelSupports\Http\Responses;


/**
 *
 * @author  WilsonParker
 * @added   2021/10/19
 * @updated 2021/10/19
 */
class ResponseErrorTemplate extends ResponseTemplate
{

    public function __construct(\Throwable $t, $data = null, $header = [], $option = 0)
    {
        $this->httpCode = self::HTTP_BAD_REQUEST;
        $this->code = $t->getCode();
        $this->message = $t->getMessage();
        $this->data = $data;
        $this->header = $header;
        $this->option = $option;
    }

}
