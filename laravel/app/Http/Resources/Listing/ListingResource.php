<?php

namespace App\Http\Resources\Listing;

use App\Http\Resources\Admin\Category\CategoryResource;
use App\Http\Resources\Base\BaseResource;
use Illuminate\Http\Request;

class ListingResource extends BaseResource
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
            'unique_number' => $this->unique_number,
            'name' => $this->name,
            'description' => $this->description,
            'image_url' => $this->getImageUrl(),
            'image_urls' => $this->getImageUrls(),
            'thumbnail_url' => $this->getThumbnailUrl(),
            'status' => $this->status?->value,
            'status_description' => $this->status?->description(),
            'is_approved' => $this->isApproved(),
            'is_pending' => $this->isPending(),
            'is_rejected' => $this->isRejected(),
            'category_uuid' => $this->category_uuid,
            'country_uuid' => $this->country_uuid,
            'city_uuid' => $this->city_uuid,
            'district_uuid' => $this->district_uuid,

            // Relationships
            'category' => new CategoryResource($this->whenLoaded('category')),
            'country' => [
                'uuid' => $this->country?->uuid,
                'name' => $this->country?->name,
                'code' => $this->country?->code,
            ],
            'city' => [
                'uuid' => $this->city?->uuid,
                'name' => $this->city?->name,
                'plate_code' => $this->city?->plate_code,
            ],
            'district' => [
                'uuid' => $this->district?->uuid,
                'name' => $this->district?->name,
            ],
            'user' => [
                'uuid' => $this->user?->uuid,
                'name' => $this->user?->name,
                'email' => $this->when($request->user()?->uuid === $this->user_uuid, $this->user?->email),
            ],

            // Approval info (only for admin or owner)
            'approved_by' => $this->when(
                $request->user()?->hasRole('admin') || $request->user()?->uuid === $this->user_uuid,
                [
                    'uuid' => $this->approvedBy?->uuid,
                    'name' => $this->approvedBy?->name,
                ]
            ),
            'approved_at' => $this->when(
                $request->user()?->hasRole('admin') || $request->user()?->uuid === $this->user_uuid,
                $this->approved_at?->toISOString()
            ),
            'rejection_reason' => $this->when(
                ($request->user()?->hasRole('admin') || $request->user()?->uuid === $this->user_uuid) && $this->isRejected(),
                $this->rejection_reason
            ),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
