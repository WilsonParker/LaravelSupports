<?php

namespace LaravelSupports\AI\OpenAI\Enums;

use App\Http\Resources\BaseResources;
use App\Http\Resources\Prompt\PromptChatGenerateResultResources;
use App\Http\Resources\Prompt\PromptCompletionGenerateResultResources;
use App\Http\Resources\Prompt\PromptImageGenerateResultResources;

enum OpenAITypes: string
{
    case Image = 'image';
    case Chat = 'chat';
    case Completion = 'completion';

    public function newResources($data): BaseResources
    {
        return match ($this) {
            self::Image => new PromptImageGenerateResultResources($data),
            self::Chat => new PromptChatGenerateResultResources($data),
            self::Completion => new PromptCompletionGenerateResultResources($data),
        };
    }
}
