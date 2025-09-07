<?php

namespace Database\Factories\District;

use App\Models\City\City;
use App\Models\District\District;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<District>
 */
class DistrictFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = District::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->city(),
            'city_uuid' => City::factory(),
            'status' => $this->faker->randomElement([0, 1]),
        ];
    }

    /**
     * Indicate that the district is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    /**
     * Indicate that the district is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }

    /**
     * Indicate that the district belongs to a specific city.
     */
    public function forCity(string $cityUuid): static
    {
        return $this->state(fn (array $attributes) => [
            'city_uuid' => $cityUuid,
        ]);
    }
}
