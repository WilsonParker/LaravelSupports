<?php

namespace LaravelSupports\app\Models\Resources\Contracts;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

interface ResourceableContract
{
    public function saveResource(string $origin, ?string $name = null): ResourceContract;

    public function saveResourceWithFile(UploadedFile $file, ?string $name = null): ResourceContract;

    public function getResourcePath(): string;

    public function getResourceStorage(): string;

}
