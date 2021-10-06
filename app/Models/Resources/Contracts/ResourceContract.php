<?php

namespace LaravelSupports\Models\Resources\Contracts;

interface ResourceContract
{
    public function resourceable(): \Illuminate\Database\Eloquent\Relations\MorphTo;

    public function getStorageDisk(): string;

    public function getUrl(): string;

}
