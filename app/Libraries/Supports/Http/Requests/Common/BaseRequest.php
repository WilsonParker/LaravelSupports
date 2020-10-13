<?php

namespace LaravelSupports\Libraries\Supports\Http\Requests\Common;

use Closure;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use LaravelSupports\Libraries\Supports\Http\Responses\ResponseTemplate;

abstract class BaseRequest extends FormRequest
{
    protected array $rules = [];
    protected array $messages = [];
    protected $validatorCallback;

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
     * @param mixed $validatorCallback
     */
    public function setValidatorCallback($validatorCallback)
    {
        $this->validatorCallback = $validatorCallback;
    }

    /**
     * 중복 방지 validation callback 제공
     *
     * @param $from
     * @param $to
     * @param $message
     * @return Closure
     * @author  dew9163
     * @added   2020/06/22
     * @updated 2020/06/22
     */
    protected function getConflictValidationCallback($from, $to, $message)
    {
        return function ($validator) use ($from, $to, $message) {
            $validator->after(function (Validator $validator) use ($from, $to, $message) {
                $validationData = $validator->getData();
                if (isset($validationData[$from]) && isset($validationData[$to])) {
                    $validator->errors()->add($from, $message);
                }
            });
        };
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator(\Illuminate\Validation\Validator $validator)
    {
        if (isset($this->validatorCallback)) {
            $callback = $this->validatorCallback;
            $callback($validator);
        }
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
