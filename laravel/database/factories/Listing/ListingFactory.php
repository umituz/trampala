<?php

namespace Database\Factories\Listing;

use App\Enums\Listing\ListingStatusEnum;
use App\Models\Category\Category;
use App\Models\City\City;
use App\Models\District\District;
use App\Models\Listing\Listing;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Listing>
 */
class ListingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Listing::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = Category::whereNotNull('parent_uuid')->pluck('uuid');
        $cities = City::pluck('uuid');
        $districts = District::pluck('uuid');
        $users = User::pluck('uuid');

        return [
            'uuid' => $this->faker->uuid(),
            'unique_number' => Listing::generateUniqueNumber(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraphs(3, true),
            'image_path' => $this->faker->imageUrl(640, 480, 'business', true),
            'category_uuid' => $categories->isNotEmpty() ? $this->faker->randomElement($categories) : Category::factory(),
            'city_uuid' => $cities->isNotEmpty() ? $this->faker->randomElement($cities) : City::factory(),
            'district_uuid' => $districts->isNotEmpty() ? $this->faker->randomElement($districts) : District::factory(),
            'user_uuid' => $users->isNotEmpty() ? $this->faker->randomElement($users) : User::factory(),
            'status' => $this->faker->randomElement(ListingStatusEnum::cases()),
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ];
    }

    /**
     * Indicate that the listing is approved.
     */
    public function approved(): static
    {
        $users = User::pluck('uuid');

        return $this->state(fn (array $attributes) => [
            'status' => ListingStatusEnum::APPROVED,
            'approved_by' => $users->isNotEmpty() ? $this->faker->randomElement($users) : User::factory(),
            'approved_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Indicate that the listing is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ListingStatusEnum::PENDING,
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Indicate that the listing is rejected.
     */
    public function rejected(): static
    {
        $users = User::pluck('uuid');

        return $this->state(fn (array $attributes) => [
            'status' => ListingStatusEnum::REJECTED,
            'approved_by' => $users->isNotEmpty() ? $this->faker->randomElement($users) : User::factory(),
            'approved_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the listing belongs to a specific user.
     */
    public function forUser(string $userUuid): static
    {
        return $this->state(fn (array $attributes) => [
            'user_uuid' => $userUuid,
        ]);
    }

    /**
     * Indicate that the listing belongs to a specific category.
     */
    public function forCategory(string $categoryUuid): static
    {
        return $this->state(fn (array $attributes) => [
            'category_uuid' => $categoryUuid,
        ]);
    }

    /**
     * Indicate that the listing is in a specific city.
     */
    public function forCity(string $cityUuid): static
    {
        return $this->state(fn (array $attributes) => [
            'city_uuid' => $cityUuid,
        ]);
    }

    /**
     * Indicate that the listing is in a specific district.
     */
    public function forDistrict(string $districtUuid): static
    {
        return $this->state(fn (array $attributes) => [
            'district_uuid' => $districtUuid,
        ]);
    }
}
