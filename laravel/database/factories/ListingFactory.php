<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraphs(3, true),
            'image_path' => 'listings/sample-' . fake()->numberBetween(1, 10) . '.jpg',
            'category_uuid' => \App\Models\Category\Category::factory(),
            'city_uuid' => \App\Models\City\City::factory(),
            'district_uuid' => \App\Models\District\District::factory(),
            'user_uuid' => \App\Models\User\User::factory(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ];
    }

    /**
     * Create a pending listing
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Create an approved listing
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_by' => \App\Models\User\User::factory(),
            'approved_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Create a rejected listing
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'approved_by' => \App\Models\User\User::factory(),
            'approved_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'rejection_reason' => fake()->sentence(8),
        ]);
    }

    /**
     * Create listing for specific user
     */
    public function forUser(string $userUuid): static
    {
        return $this->state(fn (array $attributes) => [
            'user_uuid' => $userUuid,
        ]);
    }
}
