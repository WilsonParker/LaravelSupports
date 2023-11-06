<?php


namespace LaravelSupports\Objects\Traits;

trait Fillable
{
    public function fill(array $attributes): void
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

}
