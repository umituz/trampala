<?php

namespace App\Http\Resources\Admin\District;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * District Collection Resource for API responses.
 *
 * @package App\Http\Resources\Admin\District
 */
class DistrictCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request The HTTP request instance
     * @return array<int|string, mixed> The transformed resource collection
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}