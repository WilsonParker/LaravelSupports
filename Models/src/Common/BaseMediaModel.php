<?php

namespace LaravelSupports\Models\Common;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class BaseMediaModel implements HasMedia
{
    use InteractsWithMedia;
}
