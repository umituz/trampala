<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true);
        
        return [
            'name' => ucwords($name),
            'slug' => \Str::slug($name),
            'description' => fake()->sentence(10),
            'status' => fake()->boolean(90) ? 1 : 0, // 90% chance to be active
            'parent_uuid' => null, // Will be set by seeder for child categories
        ];
    }

    /**
     * Create a root category (no parent)
     */
    public function root(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_uuid' => null,
        ]);
    }

    /**
     * Create a child category
     */
    public function child(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_uuid' => \App\Models\Category\Category::factory(),
        ]);
    }

    /**
     * Create an active category
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }
}
