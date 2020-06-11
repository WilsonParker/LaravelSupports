<?php


namespace LaravelSupports\Libraries\Codes\Contracts;


interface CodeGeneratable
{

    public function isExists($code);

    public function setCode($code);
}
