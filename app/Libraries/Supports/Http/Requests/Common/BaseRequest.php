<?php

namespace LaravelSupports\Libraries\Supports\Http\Requests\Common;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use LaravelSupports\Libraries\Supports\Http\Responses\ResponseTemplate;

abstract class BaseRequest extends FormRequest
{
    protected $rules = [];
    protected array $messages = [];

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->init();
    }

    protected function init()
    {
    }

    public function rules()
    {
        return $this->rules;
    }

    public function messages()
    {
        return $this->messages;
    }

    /**
     * throw an error with ResponseTemplate
     * message contains validation message
     *
     * @param Validator $validator
     * @return void
     * @author  dew9163
     * @added   2020/04/27
     * @updated 2020/04/27
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(new ResponseTemplate(Response::HTTP_BAD_REQUEST, "", $validator->getMessageBag()->first()));
    }

}
