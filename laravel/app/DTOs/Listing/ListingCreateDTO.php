<?php

namespace App\DTOs\Listing;

use Illuminate\Http\UploadedFile;

class ListingCreateDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $category_uuid,
        public readonly string $country_uuid,
        public readonly string $city_uuid,
        public readonly ?string $district_uuid,
        public readonly UploadedFile $image,
        public readonly string $user_uuid,
    ) {}

    /**
     * Create DTO from validated request data
     */
    public static function fromRequest(array $data, UploadedFile $image, string $userUuid): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'],
            category_uuid: $data['category_uuid'],
            country_uuid: $data['country_uuid'],
            city_uuid: $data['city_uuid'],
            district_uuid: $data['district_uuid'] ?? null,
            image: $image,
            user_uuid: $userUuid
        );
    }

    /**
     * Convert to array for model creation
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'category_uuid' => $this->category_uuid,
            'country_uuid' => $this->country_uuid,
            'city_uuid' => $this->city_uuid,
            'district_uuid' => $this->district_uuid,
            'user_uuid' => $this->user_uuid,
        ];
    }
}