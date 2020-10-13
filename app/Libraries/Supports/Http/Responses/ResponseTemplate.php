<?php


namespace LaravelSupports\Libraries\Supports\Http\Responses;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\ResponseTrait;

/**
 * Result object of API
 *
 * @author  dew9163
 * @added   2020/03/26
 * @updated   2020/03/26
 * set properties public to protected
 * @updated 2020/05/14
 */
class ResponseTemplate extends Response implements Responsable, Arrayable
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
    protected $httpCode;
    protected $code;
    protected $message;
    protected $data;
    protected $header;
    protected $option;

    public function __construct($httpCode = Response::HTTP_OK, $code = "", $message = "", $data = null, $header = [], $option = 0)
    {
        $this->httpCode = $httpCode;
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
        $this->header = $header;
        $this->option = $option;
        $this->init();
    }

    protected function init() {

    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
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

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    /**
     * @return int
     */
    public function getOption(): int
    {
        return $this->option;
    }

}
