<?php

namespace App\Http\Resources\Admin\City;

use App\Http\Resources\Base\BaseResource;
use Illuminate\Http\Request;

class CityResource extends BaseResource
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
            'plate_code' => $this->plate_code,
            'is_active' => $this->is_active,

            // Country
            'country' => $this->when(
                $this->relationLoaded('country'),
                function () {
                    return [
                        'uuid' => $this->country->uuid,
                        'code' => $this->country->code,
                        'name' => $this->country->name,
                    ];
                }
            ),

            // Districts
            'districts' => $this->when(
                $this->relationLoaded('districts'),
                function () {
                    return $this->districts->map(function ($district) {
                        return [
                            'uuid' => $district->uuid,
                            'name' => $district->name,
                            'is_active' => $district->is_active,
                        ];
                    });
                }
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
