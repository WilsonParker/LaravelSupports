<?php


namespace LaravelSupports\Libraries\Supports\Http;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Json 으로 변환하기 위한 추상 클래스
 *
 * @author  dew9163
 * @added   2020/03/12
 * @updated 2020/03/12
 */
class JsonObject implements Responsable, Arrayable
{
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
    // Response code 입니다
    public $code;
    public $message;
    public $data;
    public $header;
    public $option;

    public function __construct($code = Response::HTTP_OK, $message = "", $data = null, $header = [], $option = 0)
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
        $this->header = $header;
        $this->option = $option;
    }

    /**
     * 저장 되어있는 message 와 data 를 array 로 return 합니다
     *
     * @return  array
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     */
    private function getData() {
        return [
            "message" => $this->message,
            "data" => $this->data,
        ];
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return response()->json($this->getData(), $this->code, $this->header, $this->option);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        // TODO: Implement toArray() method.
    }
}
