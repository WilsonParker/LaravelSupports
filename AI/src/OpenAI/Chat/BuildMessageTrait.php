<?php

namespace LaravelSupports\AI\OpenAI\Chat;

trait BuildMessageTrait
{
    public function buildMessage(string $role, string $content): array
    {
        return [
            'role' => $role,
            'content' => $content,
        ];
    }
}
