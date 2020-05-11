<?php

/**
 * @class   StringHelper.php
 * @author  WilsonParker
 * @brief
 * String 관련 함수를 제공합니다
 * @see
 * @todo
 * @bug
 * @create  20181227
 * @update  20200310
 **/

namespace App\LaravelSupports\Library\Supports\Data;

use Illuminate\Support\Arr;

class StringHelper
{

    /**
     * @param String $haystack
     * 검사할 문자열
     * @param String $needle
     * 찾을 문자열
     * @return  boolean
     * @author  WilsonParker
     * @brief
     * $haystack 에 $neelde 이 포함되는지 찾습니다
     * ex) C|U 에서 C가 포함되는지 찾습니다 = true
     * @see
     * @todo
     * @bug
     * @create  20181227
     * @update  20181227
     **/
    public static function contains(String $haystack, String $needle)
    {
        return strpos($haystack, $needle) === false ? false : true;
    }

    /**
     * @param String $reg
     * @return  String $key
     * @author  WilsonParker
     * @brief
     *
     * @see
     * @todo
     * @bug
     * @create  20181227
     * @update  20181227
     **/
    protected function matchesKey(String $reg, String $key)
    {
        preg_match_all($reg, $key, $matches);
        return $matches[0][0];
    }

    public static function defaultString(String $str, String $def)
    {
        return is_null($str) ? $def : $str;
    }

    public static function explodeWithTrim(String $delimiter, String $str) {
        return array_map("trim", explode($delimiter, $str));
    }
}
