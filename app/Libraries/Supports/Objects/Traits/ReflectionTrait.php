<?php


namespace LaravelSupports\Libraries\Supports\Objects\Traits;


trait ReflectionTrait
{
    public function getProps()
    {
        return array_keys(get_object_vars($this));
    }

    public function bind($data, $callback)
    {
        foreach ($this->getProps() as $prop) {
            $callback($data, $prop);
        }
        $this->afterBind();
    }

    public function bindStd($std)
    {
        $callback = function ($data, $prop) {
            if (isset($data->{$prop})) {
                $this->{$prop} = $data->{$prop};
            }
        };
        $this->bind($std, $callback);
    }

    public function bindJson($json)
    {
        $data = json_decode($json, true);
        $this->bindArray($data);
    }

    public function bindArray($arr)
    {
        $callback = function ($data, $prop) {
            if (isset($data[$prop])) {
                $this->{$prop} = $data[$prop];
            }
        };
        $this->bind($arr, $callback);
    }

    public function afterBind()
    {

    }
}
