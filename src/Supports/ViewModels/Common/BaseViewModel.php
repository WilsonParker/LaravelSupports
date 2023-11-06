<?php

namespace LaravelSupports\Supports\ViewModels\Common;

use Illuminate\Support\Str;
use Spatie\ViewModels\ViewModel;

/**
 *
 * @author  WilsonParker
 * @added   2019-08-06
 * @updated 2019-08-06
 */
abstract class BaseViewModel extends ViewModel
{
    protected $ignoreContains = [];

    protected function shouldIgnore(string $methodName): bool
    {
        if (Str::startsWith($methodName, '__')) {
            return true;
        }

        $contains = collect($this->ignoreContains)->map(function ($item) {
            return strtoupper($item);
        })->toArray();

        return Str::contains(strtoupper($methodName), $contains) || in_array($methodName, $this->ignoredMethods());
    }

    /**
     * parent::toArray() 를 실행합니다
     *
     * @return  array
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     */
    public function toArray(): array
    {
        $this->onArrayBefore($this);
        return parent::toArray();
    }

    /**
     * toArray() 를 실행하기 전에 실행되는 함수 입니다
     *
     * @param   $viewModel
     * @return  void
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     */
    protected function onArrayBefore($viewModel)
    {

    }

    /**
     * $list 를 json 으로 변환해주는 함수 입니다
     *
     * @param   $list
     * @param   $setKeyCallback
     * function ($item)
     * $list 데이터 각각의 요소인 $item 을 넘겨 받으며
     * array $json 의 key 에 해당하는 값을 return 해주어야 합니다
     * @param   $getItemCallback
     * function ($item)
     * $list 데이터 각각의 요소인 $item 을 넘겨 받으며
     * array $json 의 value 에 해당하는 값을 return 해주어야 합니다
     * @return  false|string
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     */
    protected function convertListToJson($list, $setKeyCallback, $getItemCallback)
    {
        $json = [];
        foreach ($list as $item) {
            $json[$setKeyCallback($item)] = $getItemCallback($item);
        }
        return $this->defaultJsonEncode($json);
    }

    /**
     * Json 으로 encode 하는 기본 함수 입니다
     *
     * @param   $data
     * @return  false|string
     * @author  WilsonParker
     * @added   2019-08-28
     * @updated 2019-08-28
     */
    protected function defaultJsonEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}
