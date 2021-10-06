<?php

namespace LaravelSupports\Models\Resources\Traits;

use Illuminate\Support\Facades\Storage;

/**
 * resource model 에서 사용하는 functions
 * @author  dev9163
 * @added   2021/10/06
 * @updated 2021/10/06
 */
trait ResourceTrait
{
    public function resourceable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function getStorageDisk(): string
    {
        return 'public';
    }

    public function getStorage(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk($this->getStorageDisk());
    }

    public function getUrl(): string
    {
        return $this->getStorage()->url($this->getPath());
    }

    public function getPath(): string
    {
        return "$this->path/$this->name";
    }

    public function downloads(): string
    {
        return $this->getStorage()->download($this->getPath());
    }
}
