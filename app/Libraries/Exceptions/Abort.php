<?php

namespace LaravelSupports\Libraries\Exceptions;

use Exception;
use LaravelSupports\Libraries\Exceptions\Contracts\Abortable;

class Abort extends Exception implements Abortable
{

}
