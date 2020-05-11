<?php


namespace App\LaravelSupports\Library\Supports\Data;


class UrlHelper
{
    private const ENTITIES = ['%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D', '%20'];
    private const REPLACEMENTS = ['!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]", " "];

    /**
     * url encode
     *
     * @param   $string
     * @param bool $onlySpecialCharacters
     * true 일 경우 특수 문자만 encoding 합니다
     * @return  string
     * @author  dew9163
     * @added   2020/03/24
     * @updated 2020/03/24
     */
    public static function urlEncode($string, bool $onlySpecialCharacters = false)
    {
        return $onlySpecialCharacters ? str_replace(self::REPLACEMENTS, self::ENTITIES, $string) : urlencode($string);
    }

    /**
     * url decode
     *
     * @param   $string
     * @param bool $onlySpecialCharacters
     * true 일 경우 특수 문자만 decoding 합니다
     * @return string|string[]
     * @author  dew9163
     * @added   2020/03/24
     * @updated 2020/03/24
     */
    public static function urlDecode($string, bool $onlySpecialCharacters = false)
    {
        return $onlySpecialCharacters ? str_replace(self::ENTITIES, self::REPLACEMENTS, $string) : urldecode($string);
    }

    public static function replacePercent($string, $other = "_")
    {
        return str_replace("%", $other, $string);
    }

    public static function replaceToPercent($string, $other = "_")
    {
        return str_replace($other, "%", $string);
    }

}
