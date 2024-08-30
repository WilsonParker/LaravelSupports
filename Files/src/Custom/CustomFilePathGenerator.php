<?php

namespace LaravelSupports\Files\Custom;

use Illuminate\Support\Arr;
use LaravelSupports\Files\Contracts\HasMediaPath;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class CustomFilePathGenerator extends DefaultPathGenerator
{
    protected function getBasePath(Media $media): string
    {
        $prefix = config('media-library.prefix', '');

        if ($media->model instanceof HasMediaPath) {
            $suffix = $media->model->getMediaPath();
        } else {
            $suffix = '';
        }

        return Arr::join(Arr::where([$prefix, $suffix, $media->getKey()], fn($item) => isset($item) && $item !== ''), '/');
    }

}
