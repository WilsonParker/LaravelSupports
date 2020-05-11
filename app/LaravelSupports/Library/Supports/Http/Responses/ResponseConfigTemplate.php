<?php


namespace App\LaravelSupports\Library\Supports\Http\Responses;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Result object of API
 *
 * @author  dew9163
 * @added   2020/04/24
 * @updated 2020/04/24
 */
class ResponseConfigTemplate extends ResponseTemplate
{

    public function __construct($httpCode = Response::HTTP_OK, $prefix = "", $code = "", $message = "", $data = null, $header = [], $option = 0)
    {
        $this->httpCode = $httpCode;
        $this->code = config($prefix.".".$code);
        $this->message = config($prefix.".".$message);
        $this->data = $data;
        $this->header = $header;
        $this->option = $option;
    }

}
