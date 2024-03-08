<?php

namespace LaravelSupports\AI\OpenAI\Repositories;

use LaravelSupports\AI\Models\OpenAiKeyStack;
use LaravelSupports\AI\OpenAI\Exceptions\NotFoundOpenAIStackException;
use LaravelSupports\Database\Repositories\BaseRepository;

class OpenAiKeyRepository extends BaseRepository
{
    public function __construct(protected string $openAiKeyModel, protected string $openAiKeyStackModel)
    {
        parent::__construct($this->openAiKeyModel);
    }

    /**
     * @throws \Throwable
     */
    public function getOpenAiKeyStackModel(string $date, ?OpenAiKeyStack $except = null): OpenAiKeyStack
    {
        $model = $this->openAiKeyStackModel::where('date', $date)
                                           ->whereHas('openAiKey', function ($query) {
                                               $query->where('is_enabled', true);
                                           })
                                           ->when($except !== null, function ($query) use ($except) {
                                               $query->where('id', '!=', $except->getKey());
                                           })
                                           ->lockForUpdate()
                                           ->orderBy('call')
                                           ->first();
        if ($model !== null) {
            return $model;
        }

        if ($this->openAiKeyModel::where('is_enabled', true)->count() === 0) {
            throw new NotFoundOpenAIStackException();
        } else {
            $this->openAiKeyModel::all()->each(function ($key) use ($date, &$model) {
                $model = $this->openAiKeyStackModel::create([
                    'open_ai_key_id' => $key->id,
                    'call'           => 0,
                    'date'           => $date,
                ]);
            });

        }

        return $this->getOpenAiKeyStackModel($date, $except);
    }

    public function incrementCall(int $id): bool
    {
        return $this->openAiKeyStackModel::find($id)->increment('call');
    }

    public function disableKey(int $id): bool
    {
        $stack = $this->openAiKeyStackModel::find($id);
        return $stack->openAiKey->update([
            'is_enabled' => false,
        ]);
    }
}
