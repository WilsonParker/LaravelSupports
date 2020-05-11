<?php


namespace App\LaravelSupports\Library\Supports\Http\Responses;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Illuminate\Http\ResponseTrait;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Result object of API
 *
 * @author  dew9163
 * @added   2020/03/26
 * @updated 2020/03/26
 */
class ResponseTemplate implements Responsable, Arrayable
{
    use ResponseTrait;
    /*
      *
      * {
      *  code : 200 | 500 ... ,
      *  message : "get app intro image success",
      *  data : [
      *      "imageUrl" : "https://img. ...",
      *      "key..." : "value...",
      *  ]
      * }
      *
      */
    // Http code 입니다
    public $httpCode;
    public $code;
    public $message;
    public $data;
    public $header;
    public $option;

    public function __construct($httpCode = Response::HTTP_OK, $code = "", $message = "", $data = null, $header = [], $option = 0)
    {
        $this->httpCode = $httpCode;
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
        $this->header = $header;
        $this->option = $option;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), $this->httpCode, $this->header, $this->option);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            "code" => $this->code,
            "message" => $this->message,
            "data" => $this->data,
        ];
    }
}
