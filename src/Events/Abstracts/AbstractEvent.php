<?php


namespace LaravelSupports\Events\Abstracts;


use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractEvent
{
    abstract public function handle();
}
