<?php

namespace App\Repositories\Admin\Category;

use App\Models\Category\Category;
use App\Repositories\Base\BaseRepository;
use App\Traits\Repository\HasPagination;
use App\Traits\Repository\HasActiveScope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    use HasPagination, HasActiveScope;
    
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all categories with pagination
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->paginateWithRelations(
            relations: ['parent', 'children'],
            perPage: $perPage,
            orderBy: 'name',
            orderDirection: 'asc'
        );
    }

    /**
     * Get active categories only
     */
    public function getActiveCategories(int $perPage = 15): LengthAwarePaginator
    {
        return $this->paginateActive(
            perPage: $perPage,
            relations: ['parent', 'children']
        );
    }

    /**
     * Get root categories (no parent)
     */
    public function getRootCategories(): Collection
    {
        return $this->model
            ->active()
            ->root()
            ->with(['children' => function ($query) {
                $query->active()->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get child categories of a parent
     */
    public function getChildCategories(string $parentUuid): Collection
    {
        return $this->model
            ->active()
            ->where('parent_uuid', $parentUuid)
            ->orderBy('name')
            ->get();
    }

    /**
     * Find category by slug
     */
    public function findBySlug(string $slug): ?Category
    {
        return $this->model
            ->where('slug', $slug)
            ->with(['parent', 'children'])
            ->first();
    }

    /**
     * Get categories with children count
     */
    public function getCategoriesWithChildrenCount(): Collection
    {
        return $this->model
            ->active()
            ->withCount('children')
            ->orderBy('name')
            ->get();
    }
}
