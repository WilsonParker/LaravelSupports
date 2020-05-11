<?php

/**
 * @class   ArrayHelper.php
 * @author  WilsonParker
 * @brief   Array 관련 function 을 제공해줍니다
 * @see
 * @todo
 * @bug
 * @create  20181224
 * @update  20181224
 **/

namespace App\LaravelSupports\Library\Supports\Data;

class ArrayHelper
{

    public static function merge(array...$arr)
    {
        return array_merge($arr);
    }

    /**
     * @param array &$arr
     * key 를 제거할 array
     * @param array $keys
     * 제거할 key array
     * @author  WilsonParker
     * @brief   $keys 에 저장된 key 들을 $arr 에서 제거합니다
     * @see
     * @todo
     * @bug
     * @create  20181224
     * @update  20181224
     **/
    public static function removeValues(array &$arr, array $keys)
    {
        foreach ($keys as $key) {
            if (in_array($key, $arr)) {
                array_splice($arr, array_search($key, $arr), 1);
            }
        }
    }

    /**
     * @param Array &$arr
     * @param Array $keys
     * @author  WilsonParker
     * @brief   key => value 로 구성된 array 에서 $keys 에 포함된 key 들을 $arr 에서 제거합니다
     * @see
     * @todo
     * @bug
     * @create  20181224
     * @update  20181224
     **/
    public static function removeKeyAndValues(array &$arr, array $keys)
    {
        // array_diff_key() expected an associative array.
        $assocKeys = array();
        foreach ($keys as $key) {
            $assocKeys[$key] = true;
        }
        $arr = array_diff_key($arr, $assocKeys);
    }

    /**
     * Array 의 values 가 null 을 포함하고 있는지 확인 합니다
     *
     * @param array $array
     * @return bool
     * @author  dew9163
     * @added   2020/04/16
     * @updated 2020/04/16
     */
    public static function containsArrayValuesNull(array $array)
    {
        foreach (array_values($array) as $values) {
            if (self::containsValuesNull($values)) {
                return true;
            }
        }
        return false;
    }

    /**
     * values 가 null 을 포함하고 있는지 확인 합니다
     *
     * @param array $values
     * @return bool
     * @author  dew9163
     * @added   2020/04/16
     * @updated 2020/04/16
     */
    public static function containsValuesNull(array $values)
    {
        foreach ($values as $value) {
            if (is_null($value)) {
                return true;
            }
        }
        return false;
    }

}
