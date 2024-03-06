<?php

namespace LaravelSupports\AI\OpenAI\Abstracts;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use RecipeP\Models\OpenAI\OpenAiKeyStack;
use LaravelSupports\AI\OpenAI\Contracts\ApiRecursiveContract;
use LaravelSupports\AI\OpenAI\Repositories\OpenAiKeyRepository;
use Throwable;

abstract class AbstractRecursiveApiService extends AbstractApiService implements ApiRecursiveContract
{
    protected int $maxTry = 5;

    public function __construct(
        private readonly OpenAiKeyRepository $openAiKeyRepository,
    )
    {
    }

    /**
     * @param array                                      $attributes
     * @param \RecipeP\Models\OpenAI\OpenAiKeyStack|null $key
     * @param int|null                                   $try
     * @return array
     * @throws \Throwable
     * @author  allen
     * @added   2023/08/24
     * @updated 2023/08/24
     */
    public function callApiRecursive(
        array           $attributes,
        ?OpenAiKeyStack $key = null,
        ?int            $try = 0
    ): array
    {
        $keyModel = $this->changeApiKey($key);
        try {
            $this->getOpenAiKeyRepository()->incrementCall($keyModel->getKey());
            return $this->call($attributes, $keyModel->getApiKey());
        } catch (Throwable $throwable) {
            if ($throwable->getErrorType() == 'insufficient_quota') {
                $this->getOpenAiKeyRepository()->disableKey($keyModel->getKey());
            }
            if ($try < $this->maxTry) {
                return $this->callApiRecursive($attributes, $keyModel, $try + 1);
            }
            throw $throwable;
        }
    }

    protected function changeApiKey(?OpenAiKeyStack $model = null): OpenAiKeyStack
    {
        $date = now()->format('Y-m-d');
        $repository = $this->getOpenAiKeyRepository();
        $model = $repository->getOpenAiKeyStackModel($date, $model);
        Config::set('openai.api_key', $model->getKey());
        return $model;
    }

    protected function getOpenAiKeyRepository(): OpenAiKeyRepository
    {
        return $this->openAiKeyRepository;
    }

    protected function getCreatedDate($created): Carbon
    {
        return Carbon::createFromTimestamp($created);
    }

}
