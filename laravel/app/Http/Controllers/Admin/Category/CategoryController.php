<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Admin\Category\CategoryCollection;
use App\Http\Resources\Admin\Category\CategoryResource;
use App\Models\Category\Category;
use App\Services\Admin\Category\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    public function __construct(protected CategoryService $categoryService) {}

    /**
     * Display a listing of categories
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();

        return $this->ok(
            new CategoryCollection($categories),
            'Categories retrieved successfully.'
        );
    }

    /**
     * Display the specified category
     */
    public function show(Category $category): JsonResponse
    {
        $category = $this->categoryService->getCategoryDetails($category);

        return $this->ok(
            new CategoryResource($category),
            'Category retrieved successfully.'
        );
    }

    /**
     * Get root categories only
     */
    public function roots(): JsonResponse
    {
        $rootCategories = $this->categoryService->getRootCategories();

        return $this->ok(
            new CategoryCollection($rootCategories),
            'Root categories retrieved successfully.'
        );
    }

    /**
     * Get children of a specific category
     */
    public function children(Category $category): JsonResponse
    {
        $children = $this->categoryService->getCategoryChildren($category);

        return $this->ok(
            new CategoryCollection($children),
            'Category children retrieved successfully.'
        );
    }

    /**
     * Get category hierarchy
     */
    public function hierarchy(): JsonResponse
    {
        $hierarchy = $this->categoryService->getCategoryHierarchy();

        return $this->ok(
            new CategoryCollection($hierarchy),
            'Category hierarchy retrieved successfully.'
        );
    }

    /**
     * Get category statistics for admin dashboard
     */
    public function stats(): JsonResponse
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();

        $stats = [
            'total' => Category::count(),
            'active' => Category::count(), // All categories are considered active since there's no is_active column
            'parent_categories' => Category::whereNull('parent_uuid')->count(),
            'subcategories' => Category::whereNotNull('parent_uuid')->count(),
            'new_this_month' => Category::where('created_at', '>=', $thisMonth)->count(),
        ];

        return $this->ok($stats, 'Category statistics retrieved successfully.');
    }

    /**
     * Store a new category
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'parent_uuid' => 'nullable|exists:categories,uuid',
            'status' => 'sometimes|integer|in:0,1'
        ]);

        $category = $this->categoryService->createCategory($request->all());

        return $this->created(
            new CategoryResource($category),
            'Category created successfully.'
        );
    }

    /**
     * Update category
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:500',
            'parent_uuid' => 'nullable|exists:categories,uuid',
            'status' => 'sometimes|integer|in:0,1'
        ]);

        $updatedCategory = $this->categoryService->updateCategory($category->uuid, $request->all());

        return $this->ok(
            new CategoryResource($updatedCategory),
            'Category updated successfully.'
        );
    }

    /**
     * Update category status
     */
    public function updateStatus(Request $request, Category $category): JsonResponse
    {
        $request->validate([
            'status' => 'required|integer|in:0,1'
        ]);

        $category->update(['status' => $request->status]);

        $message = $request->status ? 'Category activated successfully.' : 'Category deactivated successfully.';
        
        return $this->ok(
            new CategoryResource($category),
            $message
        );
    }

    /**
     * Delete category
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            $this->categoryService->deleteCategory($category->uuid);
            return $this->ok(null, 'Category deleted successfully.');
        } catch (\Exception $e) {
            return $this->error(
                [$e->getMessage()], 
                'Action not allowed.', 
                422
            );
        }
    }
}
