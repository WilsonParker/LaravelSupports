<?php


namespace LaravelSupports\Objects\Traits;


trait Bindable
{
    public function bindJson($data)
    {
        $this->bind(json_decode($data, true));
    }

    public function bind($data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
