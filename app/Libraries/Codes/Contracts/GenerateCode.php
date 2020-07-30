<?php


namespace LaravelSupports\Libraries\Codes\Contracts;


interface GenerateCode
{

    public function isExists($code);

    public function setCode($code);
}
