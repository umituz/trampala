<?php

namespace App\Services\Admin\Category;

use App\Models\Category\Category;
use App\Repositories\Admin\Category\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function __construct(protected CategoryRepositoryInterface $categoryRepository) {}

    /**
     * Get all categories with pagination
     */
    public function getAllCategories(int $perPage = 15): LengthAwarePaginator
    {
        return $this->categoryRepository->getAllPaginated($perPage);
    }

    /**
     * Get active categories with pagination
     */
    public function getActiveCategories(int $perPage = 15): LengthAwarePaginator
    {
        return $this->categoryRepository->getActiveCategories($perPage);
    }

    /**
     * Get root categories for menu/navigation
     */
    public function getRootCategories(): Collection
    {
        return $this->categoryRepository->getRootCategories();
    }

    /**
     * Get child categories of a parent
     */
    public function getChildCategories(string $parentUuid): Collection
    {
        return $this->categoryRepository->getChildCategories($parentUuid);
    }

    /**
     * Create a new category
     */
    public function createCategory(array $data): Category
    {
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        return $this->categoryRepository->create($data);
    }

    /**
     * Update a category
     */
    public function updateCategory(string $uuid, array $data): Category
    {
        // Get existing category to check name change
        $category = $this->categoryRepository->findByUuid($uuid);
        
        // Regenerate slug if name changed
        if (isset($data['name']) && $data['name'] !== $category->name) {
            if (empty($data['slug'])) {
                $data['slug'] = $this->generateSlug($data['name']);
            }
        }

        return $this->categoryRepository->updateByUuid($uuid, $data);
    }

    /**
     * Delete a category
     */
    public function deleteCategory(string $uuid): bool
    {
        $category = $this->categoryRepository->findByUuid($uuid);
        
        // Check if category has children
        if ($category->hasChildren()) {
            throw new \Exception('Cannot delete category that has child categories.');
        }

        // Check if category has listings
        if ($category->listings()->exists()) {
            throw new \Exception('Cannot delete category that has listings.');
        }

        return $this->categoryRepository->deleteByUuid($uuid);
    }

    /**
     * Get category by slug
     */
    public function getCategoryBySlug(string $slug): ?Category
    {
        return $this->categoryRepository->findBySlug($slug);
    }

    /**
     * Get category by UUID
     */
    public function getCategoryById(string $uuid): ?Category
    {
        return $this->categoryRepository->findByUuid($uuid);
    }

    /**
     * Get categories with children count
     */
    public function getCategoriesWithChildrenCount(): Collection
    {
        return $this->categoryRepository->getCategoriesWithChildrenCount();
    }

    /**
     * Generate unique slug from name
     */
    private function generateSlug(string $name): string
    {
        $slug = \Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while ($this->categoryRepository->findBySlug($slug)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Get category hierarchy as a tree
     */
    public function getCategoryTree(): Collection
    {
        return $this->getRootCategories()->map(function ($category) {
            $category->load('allChildren');
            return $category;
        });
    }

    /**
     * Get category hierarchy for frontend
     */
    public function getCategoryHierarchy(): Collection
    {
        return $this->getCategoryTree();
    }

    /**
     * Get category details with relationships
     */
    public function getCategoryDetails(Category $category): Category
    {
        return $category->load(['children', 'parent']);
    }

    /**
     * Get children of a specific category
     */
    public function getCategoryChildren(Category $category): Collection
    {
        return $this->getChildCategories($category->uuid);
    }
}
