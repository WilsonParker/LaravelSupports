<?php

/**
 * @class   ArrayHelper.php
 * @author  WilsonParker
 * @brief   Array 관련 function 을 제공해줍니다
 * @create  20181224
 * @update  20181224
 **/

namespace LaravelSupports\Libraries\Supports\Data;

class ArrayHelper
{

    public static function merge(array...$arr)
    {
        return array_merge($arr);
    }

    /**
     * $keys 에 저장된 key 들을 $arr 에서 제거합니다
     *
     * @param array &$arr
     * key 를 제거할 array
     * @param array $keys
     * 제거할 key array
     * @author  WilsonParker
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
     * key => value 로 구성된 array 에서 $keys 에 포함된 key 들을 $arr 에서 제거합니다
     *
     * @param array $arr
     * @param array $keys
     * @author  WilsonParker
     * @create  20181224
     * @update  20181224
     */
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
     * $arr 과 $values 가 하나라도 포함하는지 확인 합니다
     *
     * @param array $arr
     * @param $values
     * @return bool
     * @author  dew9163
     * @added   2020/11/20
     * @updated 2020/11/20
     */
    public static function include(array $arr, $values): bool
    {
        if (is_array($values)) {
            foreach ($values as $key => $item) {
                if (self::exists($arr, $item)) {
                    return true;
                }
            }
            return false;
        } else {
            return self::exists($arr, $values);
        }
    }

    /**
     * $arr 에 value 가 존재 하는지 확인 합니다
     *
     * @param array $arr
     * @param $value
     * @return bool
     * @author  dew9163
     * @added   2020/11/20
     * @updated 2020/11/20
     */
    public static function exists(array $arr, $value): bool
    {
        foreach ($arr as $key => $item) {
            if ($item == $value) {
                return true;
            }
        }
        return false;
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

    /**
     * $array 에 $key 가 존재할 경우 해당 값을 제공하며
     * 그렇지 않을 경우 $def 를 제공 합니다
     *
     * @param $array
     * @param $key
     * @param null $def
     * @return mixed|null
     * @author  dew9163
     * @added   2020/06/19
     * @updated 2020/06/19
     */
    public static function getValueOfKeyIfExist($array, $key, $def = null)
    {
        return isset($array) ? array_key_exists($key, $array) ? $array[$key] : $def : $def;
    }

    /**
     * $from 에서 $to 에 포함되지 않는 데이터를 제공 합니다
     *
     * @param array $from
     * @param array $to
     * @return array
     * @author  dew9163
     * @added   2020/12/08
     * @updated 2020/12/08
     */
    public static function exclude(array $from, array $to): array
    {
        return collect($from)->filter(function ($item) use ($to) {
            return !self::exists($to, $item);
        })->toArray();
    }

    public static function bubbleSort(array &$list)
    {
        for ($y = count($list) - 1; $y > 0; $y--) {
            for ($i = 0; $i < $y; $i++) {
                if ($list[$i] > $list[$i + 1]) {
                    $tmp = $list[$i];
                    $list[$i] = $list[$i + 1];
                    $list[$i + 1] = $tmp;
                }
            }
        }
    }

    public static function selectionSort(array &$list)
    {
        for ($i = 0; $i < count($list) - 1; $i++) {
            $min = $i;
            for ($y = $i; $y < count($list) - 1; $y++) {
                if ($list[$min] > $list[$y]) {
                    $min = $y;
                }
            }
            if ($i != $min) {
                $tmp = $list[$i];
                $list[$i] = $list[$min];
                $list[$min] = $tmp;
            }
        }
    }

    public static function insertionSort(array &$list)
    {
        for ($i = 1; $i < count($list); $i++) {
            $tmp = $list[$i];
            $j = $i - 1;
            while ($j >= 0 && $tmp < $list[$j]) {
                $list[$j + 1] = $list[$j];
                $j--;
            }

            $list[$j + 1] = $tmp;
        }
    }
}
