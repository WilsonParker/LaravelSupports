<?php


namespace LaravelSupports\Libraries\Supports\Objects;


trait HasDataWithDefaultTrait
{
    protected function getArrayDataWithDefault($array, $key, $def = "")
    {
        return isset($array[$key]) ? $array[$key] : $def;
    }

    protected function getDataIfSet($data, $set, $default = '')
    {
        return isset($data) ? $set : $default;
    }
}
