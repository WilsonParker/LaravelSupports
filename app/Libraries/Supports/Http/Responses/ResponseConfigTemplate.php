<?php


namespace LaravelSupports\Libraries\Supports\Http\Responses;


use Illuminate\Http\Response;

/**
 * Result object of API
 *
 * @author  dew9163
 * @added   2020/04/24
 * @updated 2020/05/19
 */
class ResponseConfigTemplate extends ResponseTemplate
{

    public function __construct($httpCode = Response::HTTP_OK, $prefix = "", $code = "code", $message = "message", $data = null, $header = [], $option = 0)
    {
        $code = config($prefix . "." . $code);
        $message = config($prefix . "." . $message);
        parent::__construct($httpCode, $code, $message, $data, $header, $option);
    }

}
