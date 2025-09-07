<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\District\District>
 */
class DistrictFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->streetName(),
            'city_uuid' => \App\Models\City\City::factory(),
            'status' => fake()->boolean(95) ? 1 : 0, // 95% chance to be active
        ];
    }

    /**
     * Create an active district
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    /**
     * Create a district for a specific city
     */
    public function forCity(string $cityUuid): static
    {
        return $this->state(fn (array $attributes) => [
            'city_uuid' => $cityUuid,
        ]);
    }
}
