<?php


namespace LaravelSupports\Http\Responses;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 *
 * @author    WilsonParker
 * @added     2024/03/11
 * @updated   2024/03/11
 */
class ResponseTemplate extends JsonResponse implements Arrayable
{
    protected array $errors = [];

    public function toJson(
        $data = null,
        string $message = '',
        int $status = ResponseAlias::HTTP_OK,
        array $headers = [],
        int $options = 0,
        array $errors = [],
    ): JsonResponse
    {
        $this->message = $message;
        $this->data = $data;
        $this->errors = $errors;
        return response()->json($this->toArray(), $status, $headers, $options);
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'data' => $this->data,
            'errors' => $this->errors,
        ];
    }

}
