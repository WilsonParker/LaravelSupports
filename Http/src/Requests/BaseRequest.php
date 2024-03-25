<?php

namespace LaravelSupports\Http\Requests;

use Closure;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use LaravelSupports\Http\Requests\Exceptions\ValidationException;
use LaravelSupports\Http\Responses\ResponseTemplate;

abstract class BaseRequest extends FormRequest
{
    protected array $build = [];
    protected array $rules = [];
    protected array $messages = [];
    protected bool $isFailedRedirect = false;

    /**
     * @return bool
     */
    public function isFailedRedirect(): bool
    {
        return $this->isFailedRedirect;
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->isFailedRedirect) {
            $this->failedValidationRedirectTo($validator);
        } else {
            $this->failedValidationHttpResponse($validator);
        }
    }

    protected function failedValidationRedirectTo(Validator $validator)
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

    /**
     * throw an error with ResponseTemplate
     * message contains validation message
     *
     * @param Validator $validator
     * @return \Illuminate\Http\JsonResponse
     * @author  WilsonParker
     * @added   2020/04/27
     * @updated 2020/04/27
     */
    protected function failedValidationHttpResponse(Validator $validator)
    {
        throw new HttpResponseException(new ResponseTemplate(
            message: $validator->getMessageBag()->first(),
            status : Response::HTTP_UNPROCESSABLE_ENTITY,
            errors : collect($validator->errors())->mapWithKeys(function ($errors, $key) {
                return [$key => $errors[0]];
            })->toArray(),
        ));
        /*return ResponseTemplate::toJson(
            message: $validator->getMessageBag()->first(),
            status: Response::HTTP_UNPROCESSABLE_ENTITY,
            errors: collect($validator->errors())->mapWithKeys(function ($errors, $key) {
                return [$key => $errors[0]];
            })->toArray()
        );*/
    }

    /**
     * 중복 방지 validation callback 제공
     *
     * @param $from
     * @param $to
     * @param $message
     * @return Closure
     * @author  WilsonParker
     * @added   2020/06/22
     * @updated 2020/06/22
     */
    protected function getConflictValidationCallback($from, $to, $message): Closure
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

}
