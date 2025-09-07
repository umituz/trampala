<?php

namespace App\Models\Category;

use App\Models\Base\BaseUuidModel;
use App\Models\Listing\Listing;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends BaseUuidModel
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'parent_uuid',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_uuid');
    }

    /**
     * Get child categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_uuid');
    }

    /**
     * Get all child categories recursively
     */
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    /**
     * Get listings in this category
     */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'category_uuid');
    }

    /**
     * Check if category is active
     */
    public function isActive(): bool
    {
        return $this->status === 1;
    }

    /**
     * Check if category is root (has no parent)
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_uuid);
    }

    /**
     * Check if category has children
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Get all ancestors of this category
     */
    public function ancestors()
    {
        $ancestors = collect();
        $category = $this->parent;

        while ($category) {
            $ancestors->push($category);
            $category = $category->parent;
        }

        return $ancestors->reverse();
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to get only root categories (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_uuid');
    }

    /**
     * Scope to get categories with children
     */
    public function scopeWithChildren($query)
    {
        return $query->has('children');
    }
}