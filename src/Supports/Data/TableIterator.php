<?php

namespace LaravelSupports\Supports\Data;

class TableIterator
{
    protected $arr = array();
    protected $keys = [];
    protected $position = 0;

    public function __construct(array $arr = array())
    {
        $this->arr = $arr;
        $this->keys = array_keys($arr);
    }

    public function attach($data)
    {
        array_push($this->arr, $data);
        array_push($this->keys, key($data));
    }

    public function rewind()
    {
        $this->position = 0;
        reset($this->arr);
        reset($this->keys);
    }

    public function valid()
    {
        return false !== $this->current();
    }

    public function current()
    {
        return
            [
                current($this->keys) =>
                    current($this->arr)
            ];
    }

    public function hasMoreElement()
    {
        return (sizeof($this->arr) > $this->position);
    }

    public function next()
    {
        $this->position++;
        return
            [
                next($this->keys) =>
                    next($this->arr)
            ];
    }

    public function key()
    {
        $k = current($this->keys);
        return empty($k) ? '""' : $k;
    }

    public function value()
    {
        $v = current($this->arr);
        return empty($v) ? '""' : is_string($v) ? "'$v'" : $v;
    }

    public function position()
    {
        return $this->position;
    }
}
