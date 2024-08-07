<?php

namespace LaravelSupports\Files\Custom;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\FileNamer\DefaultFileNamer;

class CustomFileNamer extends DefaultFileNamer
{
    public function originalFileName(string $fileName): string
    {
        return Str::random(64);
    }
}
