<?php


namespace LaravelSupports\Events\Abstracts;


use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

abstract class AbstractEvent implements ShouldBroadcast
{
    abstract public function handle();
}
