<?php

namespace LaravelSupports\Libraries\Supports\Objects;

/**
 * Class 로 객체를 생성하는데 도움을 줍니다
 *
 * @author  WilsonParker
 * @class   ObjectHelper.php
 * @added   2019.03.05
 * @updated 2019.03.05
 *
 * added getProps, bind functions
 * @updated 2020.05.11
 */
class ObjectHelper
{
    /**
     * Class 와 Arguments 로 해당 객체를 생성합니다
     *
     * @param Class $cls
     * @param mixed ...$args
     * @return  Object
     * @throws \ReflectionException
     * @author  WilsonParker
     * @added   2019.03.05
     * @updated 2019.03.05
     * @bug
     * @see
     */
    public static function createInstance($cls, ...$args)
    {
        $ref = new \ReflectionClass($cls);
        return $ref->newInstance(...$args);
    }

    public static function isEmptyList($list)
    {
        return !self::isNonEmptyList($list);
    }

    public static function isNonEmptyList($list)
    {
        return isset($list) && !is_null($list) && count($list) > 0;
    }

    public static function isNonEmpty($item)
    {
        return isset($item) && !is_null($item);
    }

    public static function isEmpty($item)
    {
        return !self::isNonEmpty($item);
    }

    public static function getProps($obj)
    {
        return array_keys(get_object_vars($obj));
    }

    public static function bindStd($obj, $std)
    {
        foreach (self::getProps($obj) as $prop) {
            $obj->{$prop} = $std->{$prop};
        }
    }

    /**
     * return $def when $val is null
     * and otherwise return $val
     *
     * @param $val
     * @param $def
     * @return mixed
     * @author  dew9163
     * @added   2020/05/14
     * @updated 2020/05/14
     */
    public static function getValueWithDefault($val, $def)
    {
        return is_null($val) ? $def : $val;
    }

    public static function bindJson($obj, $json)
    {
        $data = json_decode($json, true);
        foreach (self::getProps($obj) as $prop) {
            $obj->{$prop} = $data["$prop"];
        }
    }

}
