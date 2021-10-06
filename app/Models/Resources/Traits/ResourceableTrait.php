<?php

namespace LaravelSupports\app\Models\Resources\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use LaravelSupports\app\Models\Resources\Contracts\ResourceContract;

/**
 * resource 를 저장하는 functions
 * @author  dev9163
 * @added   2021/10/06
 * @updated 2021/10/06
 */
trait ResourceableTrait
{
    public function saveResourceWithFile(UploadedFile $file, ?string $name = null): ResourceContract
    {
        $saved = Storage::disk($this->getResourceStorage())->put($this->getResourcePath(), $file);
        $resourceModel = $this->saveResource($file->getClientOriginalName(), $saved);
        $this->resource()->save($resourceModel);
        return $resourceModel;
    }

    public function getResourcePath(): string
    {
        return '';
    }

    public function getResourceStorage(): string
    {
        return 'public';
    }

}
