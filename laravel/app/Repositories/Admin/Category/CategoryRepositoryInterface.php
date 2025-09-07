<?php

namespace App\Repositories\Admin\Category;

use App\Models\Category\Category;
use App\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all categories with pagination
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get active categories only
     */
    public function getActiveCategories(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get root categories (no parent)
     */
    public function getRootCategories(): Collection;

    /**
     * Get child categories of a parent
     */
    public function getChildCategories(string $parentUuid): Collection;

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?Category;

    /**
     * Get categories with children count
     */
    public function getCategoriesWithChildrenCount(): Collection;
}
