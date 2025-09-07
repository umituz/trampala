<?php

namespace App\Http\Resources\Admin\District;

use App\Http\Resources\Base\BaseResource;
use Illuminate\Http\Request;

class DistrictResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'is_active' => $this->is_active,

            // City info
            'city' => $this->when(
                $this->relationLoaded('city') && $this->city,
                [
                    'uuid' => $this->city?->uuid,
                    'name' => $this->city?->name,
                    'plate_code' => $this->city?->plate_code,
                ]
            ),

            // Listing count
            'listings_count' => $this->when(
                $this->relationLoaded('listings'),
                $this->listings_count ?? 0
            ),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
