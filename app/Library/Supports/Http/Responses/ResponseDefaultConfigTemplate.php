<?php


namespace App\Library\Supports\Http\Responses;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use phpDocumentor\Reflection\Types\Boolean;

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
 * @author  dew9163
 * @added   2020/04/24
 * @updated 2020/04/24
 */
class ResponseDefaultConfigTemplate extends ResponseTemplate
{

    public function __construct($httpCode = Response::HTTP_OK, $prefix = "", $data = null, $header = [], $option = 0)
    {
        if ($httpCode == Response::HTTP_OK) {
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
