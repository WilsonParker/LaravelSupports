<?php


namespace LaravelSupports\Libraries\Supports\Objects;


class ReflectionObject
{

    public function getProps()
    {
        return array_keys(get_object_vars($this));
    }

    public function bindStd($std)
    {
        foreach ($this->getProps() as $prop) {
            if(isset($std->{$prop})) {
                $this->{$prop} = $std->{$prop};
            }
        }
    }

    public function bindJson($json)
    {
        $data = json_decode($json, true);
        foreach ($this->getProps() as $prop) {
            if(isset($data["$prop"])) {
                $this->{$prop} = $data["$prop"];
            }
        }
    }

}
