<?php

namespace App\Http\Resources\Admin\Category;

use App\Http\Resources\Base\BaseResource;
use Illuminate\Http\Request;

class CategoryResource extends BaseResource
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
            'description' => $this->description,
            'slug' => $this->slug,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'icon' => $this->icon,
            'parent_uuid' => $this->parent_uuid,

            // Parent category info
            'parent' => $this->when(
                $this->relationLoaded('parent') && $this->parent,
                [
                    'uuid' => $this->parent?->uuid,
                    'name' => $this->parent?->name,
                    'slug' => $this->parent?->slug,
                ]
            ),

            // Children categories (only immediate children)
            'children' => $this->when(
                $this->relationLoaded('children'),
                function () {
                    return $this->children->map(function ($child) {
                        return [
                            'uuid' => $child->uuid,
                            'name' => $child->name,
                            'slug' => $child->slug,
                            'is_active' => $child->is_active,
                            'sort_order' => $child->sort_order,
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
