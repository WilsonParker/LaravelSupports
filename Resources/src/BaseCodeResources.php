<?php

namespace LaravelSupports\Resources;

use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *      @OA\Property(property="code", type="string", description="primary key", readOnly="true", example="code"),
 * )
 * Class BaseCodeResources
 *
 * @package App\Resources
 */
abstract class BaseCodeResources extends BaseResources
{
    protected bool $showAppendFields = true;

    function appendsFields(Request $request): array
    {
        return $this->showAppendFields ? ['id' => $this->getKey()] : [];
    }

}
