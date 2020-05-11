<?php

namespace App\LaravelSupports\Library\Supports\Http\Requests;

use App\LaravelSupports\Library\Supports\Data\StringHelper;
use App\LaravelSupports\Library\Supports\Http\Contracts\RequestValueCastable;
use Illuminate\Http\Request;

/**
 * @class   RequestBinder.php
 * @author  WilsonParker
 * @brief   ValidationModel 과 사용해야 합니다
 * @see
 * @bug
 * @create  20181227
 * @update  20181228
 **/

trait RequestBinder
{

    // const DELIMITER = ":", NOT_DEFAULT_VALUE = "!";
    private $DELIMITER = ":",
            $NOT_DEFAULT_VALUE = "!";

    /**
     * @param Request $request
     * @param array $data
     * ["ADM_ID:USER_ID", "ADM_PW", "ADM_LOGIN"=>"N", "!REG_DATE"=>now()]
     * Request 에서 데이터를 가져올 name array 이며 추가적으로 rename, default, override default 를 사용할 수 있습니다
     *
     * ex) ["ADM_PART", "ADM_LOGIN"=>"N", "REG_DATE"=>now()]
     * @param RequestValueCastable|null $callable
     * $request->input 으로 데이터를 가져온 후 casting 또는 decorate 작업을 하기 위한 변수 입니다
     *
     * public function castValue(String $key, $val);
     *
     * ex)
     * $data 가 ["ADM_PART", "ADM_LOGIN"=>"N", "REG_DATE"=>now()] 일 경우
     * $key 는 "ADM_PART", $val 는 $request->input("ADM_PART") 값이 들어오게 됩니다
     * "REG_DATE"=>now() 의 경우
     * $key 는 "REG_DATE", $val 는 now() 값이 들어오게 됩니다
     *
     * public function castValue(String $key, $val) {
     *      switch ($key) {
     *          case "ADM_PART" :
     *              return "$val 부서";
     *          case "REG_DATE" :
     *              return "등록일 : $val";
     *      }
     * }
     *
     * @return  array
     * @author  WilsonParker
     * @brief
     * ["key" => "default value"]
     * ["ADM_PART", "ADM_LOGIN"=>"N", "REG_DATE"=>now()]
     * 를 입력 받아
     * [
     * 'ADM_PART' => $request->input('ADM_PART', ''),
     * 'ADM_LOGIN' => $request->input('ADM_LOGIN', 'N'),
     * 'REG_DATE' => now(),
     * ]
     * 로 변환 해줍니다
     *
     * 'USER_ID' => $request->input('INPUT_USER_ID', 'default')
     * 처럼 저장할 key 와 가져올 key 의 값이 다를 경우
     * ["USER_ID:INPUT_USER_ID" => "default"]
     * 와 같이 저장할 Key : 가져올 Key => default 형식을 사용합니다
     *
     * 'REG_DATE' => now()
     * 처럼 default 값이 아닌 아예 다른 값을 저장할 경우
     * ['!REG_DATE' => now()]
     * 와 같이 Key 앞에 ! 를 붙여서 사용합니다
     *
     * ex)
     * ["ADM_ID:USER_ID", "ADM_PW", "ADM_LOGIN"=>"N", "!REG_DATE"=>now()]
     * @see
     * @todo
     * @bug
     * @create  20181227
     * @update  20181227
     */
    public function bind(Request $request, array $data, RequestValueCastable $callable = null)
    {
        $result = [];
        foreach ($data as $key => $value) {
            $isNum = is_numeric($key);
            $rKey = $isNum ? $value : $key;
            if (StringHelper::contains($rKey, $this->NOT_DEFAULT_VALUE)) {
                $rKey = substr($rKey, 1);
                $result[$rKey] = $callable == null ? $value : $callable->castValue($rKey, $value);
            } else if (StringHelper::contains($rKey, $this->DELIMITER)) {
                $keys = explode($this->DELIMITER, $rKey);
                $rValue = $request->input($keys[1], $isNum ? '' : $value);
                $result[$keys[0]] = $callable == null ? $rValue : $callable->castValue($keys[0], $rValue);
            } else {
                $rValue = $request->input($rKey, $isNum ? '' : $value);
                $result[$rKey] = $callable == null ? $rValue : $callable->castValue($rKey, $rValue);
            }
        }
        return $result;
    }

    /**
     * array 의 value 들을 RequestValueCastable 을 이용하여 cast 하여 저장합니다
     *
     * @param array $data
     * @param RequestValueCastable|null $callable
     * @return  array
     * @author  WilsonParker
     * @added   2018.12.27
     * @updated 2018.12.27
     * @bug
     * @see
     */
    public function bindArray(array $data, RequestValueCastable $callable = null){
        $result = [];
        foreach ($data as $key => $value) {
            $result[$key] = $callable == null ? $value : $callable->castValue($key, $value);
        }
        return $result;
    }

}
