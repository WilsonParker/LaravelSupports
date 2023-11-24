<?php


namespace LaravelSupports\Objects;


class ReflectionObject
{

    public function bindStd($std)
    {
        foreach ($this->getProps() as $prop) {
            if (isset($std->{$prop})) {
                $this->{$prop} = $std->{$prop};
            }
        }
    }

    public function getProps()
    {
        return array_keys(get_object_vars($this));
    }

    public function bindJson($json)
    {
        $data = json_decode($json, true);
        foreach ($this->getProps() as $prop) {
            if (isset($data["$prop"])) {
                $this->{$prop} = $data["$prop"];
            }
        }
    }

    public function bindArray(array $arr, bool $onlyProps = false)
    {
        if ($onlyProps) {
            foreach ($this->getProps() as $prop) {
                $this->{$prop} = $arr[$prop] ?? '';
            }
        } else {
            foreach ($arr as $key => $value) {
                $this->{$key} = $value ?? '';
            }
        }
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
