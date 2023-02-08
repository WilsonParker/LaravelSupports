<?php

/**
 * @class   StringHelper.php
 * @author  WilsonParker
 * @brief
 * String 관련 함수를 제공합니다

 * @create  20181227
 * @update  20200310
 **/

namespace LaravelSupports\Libraries\Supports\Data;

class StringHelper
{
    const SPECIAL_CHARACTERS_REG = "/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i";

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
     * @create  20181227
     * @update  20181227
     **/
    public function contains(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) === false ? false : true;
    }

    /**
     * @param String $reg
     * @param string $key
     * @return  String $key
     * @author  WilsonParker
     * @brief
     *

     * @create  20181227
     * @update  20181227
     */
    protected function matchesKey(string $reg, string $key): string
    {
        preg_match_all($reg, $key, $matches);
        return $matches[0][0];
    }

    public function defaultString(string $str, string $def): string
    {
        return is_null($str) ? $def : $str;
    }

    public function explodeWithTrim(string $delimiter, string $str): array
    {
        return array_map("trim", explode($delimiter, $str));
    }

    /**
     * collection 형태의 search & replace 데이터로
     * 문자열을 변환 시킵니다
     *
     * @param $replace
     * @param $subject
     * @return string|string[]
     * @author  WilsonParker
     * @added   2020/05/18
     * @updated 2020/05/18
     * @example
     * $replace
     * [
     *  ':name' => 'john',
     *  ':age' => 25
     * ]
     * $subject
     * ':name 의 나이는 :age 입니다'
     */
    public function replaceWithCollection($replace, $subject)
    {
        return str_replace(array_keys($replace), $replace, $subject);
    }

    public function clearSpecialCharacters(string $string)
    {
        return $this->clearString($string, self::SPECIAL_CHARACTERS_REG);
    }

    public function clearString(string $string, string $reg)
    {
        return preg_replace($reg, '', $string);
    }

    public function indexOf(string $str, string $needle, int $start = 0): int
    {
        if ($start < 0) {
            return -1;
        }
        $chars = $this->strSplitUnicode($str);
        for ($i = $start; $i < sizeof($chars); $i++) {
            if ($chars[$i] === $needle) {
                return $i;
            }
        }
        return -1;
    }

    public function substr(string $str, int $start, int $end): string
    {
        $chars = $this->strSplitUnicode($str);
        $partOfStr = '';
        for ($i = $start; $i <= $end; $i++) {
            $partOfStr .= $chars[$i];
        }
        return $partOfStr;
    }

    public function strSplitUnicode($str, $l = 0)
    {
        if ($l > 0) {
            $ret = array();
            $len = mb_strlen($str, "UTF-8");
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, "UTF-8");
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }
}
