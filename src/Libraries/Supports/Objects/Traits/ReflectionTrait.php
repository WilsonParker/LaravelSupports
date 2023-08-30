<?php


namespace LaravelSupports\Libraries\Supports\Objects\Traits;


trait ReflectionTrait
{
    public function getProps(): array
    {
        return array_keys(get_object_vars($this));
    }

    public function bind($data, $callback, bool $onlyProps = false)
    {
        if ($onlyProps) {
            foreach ($this->getProps() as $prop) {
                $callback($data, $prop, null);
            }
        } else {
            foreach ($data as $key => $value) {
                $callback($data, $key, $value);
            }
        }
        $this->afterBind();
    }

    public function bindStd($std, bool $onlyProps = false)
    {
        $callback = function ($data, $prop, $value) {
            if (isset($value)) {
                $this->{$prop} = $value;
            } else if (isset($data->{$prop})) {
                $this->{$prop} = $data->{$prop};
            }
        };
        $this->bind($std, $callback, $onlyProps);
    }

    public function bindJson($json, bool $onlyProps = false)
    {
        $callback = function ($data, $prop, $value) {
            if (isset($data->{$prop})) {
                $this->{$prop} = $data->{$prop};
            }
        };
        $this->bind($json, $callback, $onlyProps);
    }

    public function bindArray($arr, bool $onlyProps = false)
    {
        $callback = function ($data, $prop, $value) {
            if (isset($data[$prop])) {
                $this->{$prop} = $data[$prop];
            }
        };
        $this->bind($arr, $callback, $onlyProps);
    }

    public function afterBind()
    {

    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->getProps() as $prop) {
            $result[$prop] = $this->{$prop};
        }
        return $result;
    }
}
