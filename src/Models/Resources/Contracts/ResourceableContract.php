<?php

namespace LaravelSupports\Models\Resources\Contracts;

use Illuminate\Http\UploadedFile;

interface ResourceableContract
{
    public function saveResource(string $origin, ?string $name = null): ResourceContract;

    public function saveResourceWithFile(UploadedFile $file, ?string $name = null): ResourceContract;

    public function getResourcePath(): string;

    public function getResourceStorage(): string;

}
