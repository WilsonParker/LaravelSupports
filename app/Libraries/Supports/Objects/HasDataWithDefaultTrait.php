<?php


namespace LaravelSupports\Libraries\Supports\Objects;


trait HasDataWithDefaultTrait
{
    protected function getArrayDataWithDefault($array, $key, $def = ""): string
    {
        return isset($array[$key]) ? $array[$key] : $def;
    }

    protected function getDataWithDefault($key, $def = ""): string
    {
        return isset($this->data[$key]) ? $this->data[$key] : $def;
    }

    protected function getDataIfSet($data, $set, $default = ''): string
    {
        return isset($data) ? $set : $default;
    }
}
