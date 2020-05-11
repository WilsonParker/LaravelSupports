<?php


namespace App\Library\Supports\Objects\Traits;


trait Bindable
{
    public function bind($data) {
        foreach ($data AS $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function bindJson($data) {
        $this->bind(json_decode($data, true));
    }
}
