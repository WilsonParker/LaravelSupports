<?php


namespace LaravelSupports\Http\Responses;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\ResponseTrait;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * Result object of API
 *
 * @author  WilsonParker
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
    protected int $httpCode;
    protected string $code;
    protected string $message;
    protected $data;
    protected array $header;
    protected int $option;

    public function __construct(int $httpCode = ResponseAlias::HTTP_OK, string $code = "", string $message = "", $data = null, array $header = [], int $option = 0)
    {
        $this->httpCode = $httpCode;
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
        $this->header = $header;
        $this->option = $option;
        $this->init();
    }

    protected function init()
    {

    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json($this->toArray(), $this->httpCode, $this->header, $this->option);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
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
