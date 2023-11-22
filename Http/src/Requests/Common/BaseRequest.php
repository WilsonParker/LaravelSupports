<?php

namespace LaravelSupports\Http\Requests\Common;

use Closure;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use LaravelSupports\Http\Responses\ResponseTemplate;

abstract class BaseRequest extends FormRequest
{
    protected array $build = [];
    protected array $rules = [];
    protected array $messages = [];
    protected bool $isFailedRedirect = false;
    protected string $prefix = '';
    protected $validatorCallback;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->init();
    }

    protected function init()
    {
    }

    public function messages(): array
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
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator(\Illuminate\Validation\Validator $validator)
    {
        if (isset($this->validatorCallback)) {
            $validator->after(function ($validator) {
                $callback = $this->validatorCallback;
                $callback($validator);
            });
        }
    }

    /**
     * @return bool
     */
    public function isFailedRedirect(): bool
    {
        return $this->isFailedRedirect;
    }

    /**
     * @param bool $isFailedRedirect
     */
    public function setIsFailedRedirect(bool $isFailedRedirect): void
    {
        $this->isFailedRedirect = $isFailedRedirect;
    }

    public function rules(): array
    {
        $method = Str::upper($this->method());
        $methodFiltered = [];
        if (Arr::exists($this->build, $method)) {
            $methodFiltered = $this->build[$method];
        } else if (Arr::exists($this->build, '')) {
            $methodFiltered = $this->build[''];
        }

        $match = collect($methodFiltered)
            ->filter(fn($item) => !$item['reg'])
            ->first(function ($values, $key) {
                return $key == $this->path() || $key == $this->prefix;
            });

        $roles = [];
        if (isset($match)) {
            $roles = $match['rules'];
        } else {
            $matchReg = collect($methodFiltered)
                ->filter(fn($item) => $item['reg'])
                ->first(function ($values, $key) {
                    return preg_match("^($key)^", $this->path());
                });
            if (isset($matchReg)) {
                $roles = $matchReg['rules'];
            }
        }
        return $roles;

        /*$pathFiltered = [];
        if (Arr::exists($methodFiltered, $this->path())) {
            $pathFiltered = $methodFiltered[$this->path()];
        } else if (Arr::exists($methodFiltered, $this->prefix)) {
            $pathFiltered = $methodFiltered[$this->prefix];
        }

        return $pathFiltered['rules'];*/
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
     * @return void
     * @author  WilsonParker
     * @added   2020/04/27
     * @updated 2020/04/27
     */
    protected function failedValidationHttpResponse(Validator $validator)
    {
        throw new HttpResponseException(new ResponseTemplate(Response::HTTP_BAD_REQUEST, "", $validator->getMessageBag()->first()));
    }

    protected function appendGet(array $rules, string $route = '', bool $isFailedRedirect = null, bool $reg = false)
    {
        $this->append('GET', $rules, $route, $isFailedRedirect, $reg);
    }

    protected function append(string $method, array $rules, string $route, bool $isFailedRedirect = null, bool $reg = false)
    {
        if ($isFailedRedirect != null) {
            $this->isFailedRedirect = $isFailedRedirect;
        }
        $method = Str::upper($method);
        if (!Arr::exists($this->build, $method)) {
            $this->build[$method] = [];
        }
        $path = Str::of(implode('/', [$this->prefix, $route]));
        $path = $path->endsWith('/') ? $path->substr(0, $path->length() - 1) : $path;

        $this->build[$method] = Arr::add($this->build[$method], "$path", [
            'reg' => $reg,
            'rules' => $rules,
        ]);
    }

    protected function appendPost(array $rules, string $route = '', bool $isFailedRedirect = null, bool $reg = false)
    {
        $this->append('POST', $rules, $route, $isFailedRedirect, $reg);
    }

    protected function appendPut(array $rules, string $route = '', bool $isFailedRedirect = null, bool $reg = false)
    {
        $this->append('PUT', $rules, $route, $isFailedRedirect, $reg);
    }

    protected function appendDelete(array $rules, string $route = '', bool $isFailedRedirect = null, bool $reg = false)
    {
        $this->append('DELETE', $rules, $route, $isFailedRedirect, $reg);
    }

    protected function appendRoute(array $rules, string $route = '', bool $isFailedRedirect = null, bool $reg = false)
    {
        $this->append('', $rules, $route, $isFailedRedirect, $reg);
    }

}
