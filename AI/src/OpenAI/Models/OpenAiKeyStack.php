<?php

namespace LaravelSupports\AI\OpenAI\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelSupports\Models\Common\BaseModel;

class OpenAiKeyStack extends BaseModel
{
    protected $table = 'open_ai_key_stacks';
    protected $with = ['openAiKey'];

    public function openAiKey(): BelongsTo
    {
        return $this->belongsTo(OpenAiKey::class);
    }

    public function getApiKey(): string
    {
        return $this->openAiKey->key;
    }
}
