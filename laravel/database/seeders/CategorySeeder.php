<?php

namespace Database\Seeders;

use App\Models\Category\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Simple Turkish categories
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Telefon ve teknoloji ürünleri'],
            ['name' => 'Araç', 'description' => 'Araba ve motorsiklet'],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create([
                'name' => $categoryData['name'],
                'slug' => \Str::slug($categoryData['name']),
                'description' => $categoryData['description'],
                'status' => 1,
                'parent_uuid' => null,
            ]);

            // Create subcategories
            $this->createSubcategories($category);
        }
    }

    /**
     * Create subcategories for each root category
     */
    private function createSubcategories(Category $parent): void
    {
        $subcategoriesMap = [
            'Elektronik' => ['Telefon', 'Laptop'],
            'Araç' => ['Araba', 'Motorsiklet'],
        ];

        if (isset($subcategoriesMap[$parent->name])) {
            foreach ($subcategoriesMap[$parent->name] as $subcategoryName) {
                Category::create([
                    'name' => $subcategoryName,
                    'slug' => $parent->slug . '-' . \Str::slug($subcategoryName),
                    'description' => $subcategoryName . ' kategorisi',
                    'status' => 1,
                    'parent_uuid' => $parent->uuid,
                ]);
            }
        }
    }
}