<?php

namespace LaravelSupports\Exceptions;

use Exception;
use LaravelSupports\Exceptions\Contracts\Abortable;

class Abort extends Exception implements Abortable
{

}
