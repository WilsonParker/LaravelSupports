<?php


namespace LaravelSupports\Codes\Contracts;


interface GenerateCode
{

    public function isExists($code): bool;

    public function setCode($code);
}
