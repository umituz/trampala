<?php

namespace App\DTOs\Listing;

use Illuminate\Http\UploadedFile;

class ListingUpdateDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $description,
        public readonly ?string $category_uuid,
        public readonly ?string $country_uuid,
        public readonly ?string $city_uuid,
        public readonly ?string $district_uuid,
        public readonly ?UploadedFile $image,
    ) {}

    /**
     * Create DTO from validated request data
     */
    public static function fromRequest(array $data, ?UploadedFile $image = null): self
    {
        return new self(
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            category_uuid: $data['category_uuid'] ?? null,
            country_uuid: $data['country_uuid'] ?? null,
            city_uuid: $data['city_uuid'] ?? null,
            district_uuid: $data['district_uuid'] ?? null,
            image: $image
        );
    }

    /**
     * Convert to array for model update (only non-null values)
     */
    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'category_uuid' => $this->category_uuid,
            'country_uuid' => $this->country_uuid,
            'city_uuid' => $this->city_uuid,
            'district_uuid' => $this->district_uuid,
        ], fn($value) => $value !== null);
    }

    /**
     * Check if has any data to update
     */
    public function hasData(): bool
    {
        return !empty($this->toArray()) || $this->image !== null;
    }
}