<?php


namespace LaravelSupports\Libraries\Supports\Http\Responses;


use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * Result object of API
 * use config data
 * set default values for code and message values
 *
 * @example
 * success.code
 * success.message
 *
 * fail.code
 * fail.message
 *
 * @author  WilsonParker
 * @added   2020/04/24
 * @updated 2020/04/24
 */
class ResponseDefaultConfigTemplate extends ResponseTemplate
{

    public function __construct($httpCode = ResponseAlias::HTTP_OK, $prefix = "", $data = null, $header = [], $option = 0)
    {
        if ($httpCode == ResponseAlias::HTTP_OK) {
            $code = "success.code";
            $message = "success.message";
        } else {
            $code = "fail.code";
            $message = "fail.message";
        }

        $this->httpCode = $httpCode;
        $this->code = config($prefix . "." . $code);
        $this->message = config($prefix . "." . $message);
        $this->data = $data;
        $this->header = $header;
        $this->option = $option;
    }

}
