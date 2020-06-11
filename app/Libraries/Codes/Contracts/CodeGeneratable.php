<?php


namespace App\Library\LaravelSupports\app\Libraries\Codes\Contracts;


interface CodeGeneratable
{

    public function isExists($code);

    public function setCode($code);
}
