<?php


namespace LaravelSupports\Libraries\Codes\Contracts;


interface GenerateCode
{

    public function isExists($code): bool;

    public function setCode($code);
}
