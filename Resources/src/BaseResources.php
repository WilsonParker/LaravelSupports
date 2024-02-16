<?php

namespace LaravelSupports\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *      @OA\Property(property="created_at", type="datetime", description="Initial creation timestamp", readOnly="true", example="2023-01-01 00:00:00"),
 *      @OA\Property(property="updated_at", type="datetime", description="Last update timestamp", readOnly="true", example="2023-01-01 00:00:00"),
 * )
 * Class BaseResources
 *
 * @package App\Resources
 */
abstract class BaseResources extends JsonResource
{
    protected bool $showDateFields = true;
    protected bool $showPrimaryField = true;
    protected string $format = 'Y-m-d H:i:s';

    public function toArray(Request $request)
    {
        if ($this->showDateFields) {
            $dateFields = [
                'created_at' => $this->formatDateTime($this->created_at),
                'updated_at' => $this->formatDateTime($this->updated_at),
            ];
        } else {
            $dateFields = [];
        }
        return array_merge(
            $this->appendsFields($request),
            $this->fields($request),
            $this->additionalFields($request),
            $dateFields
        );
    }

    protected function formatDateTime(Carbon|string $date): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        return $date->format($this->format);
    }

    abstract function appendsFields(Request $request): array;

    abstract function fields(Request $request): array;

    protected function additionalFields(Request $request): array
    {
        return [];
    }
}
