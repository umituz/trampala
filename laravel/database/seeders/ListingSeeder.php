<?php

namespace Database\Seeders;

use App\Enums\Listing\ListingStatusEnum;
use App\Enums\User\UserTypeEnum;
use App\Models\Category\Category;
use App\Models\City\City;
use App\Models\District\District;
use App\Models\Listing\Listing;
use App\Models\User\User;
use Illuminate\Database\Seeder;

class ListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::whereNotNull('parent_uuid')->get();
        $cities = City::limit(3)->get();
        $testUser = User::where('email', UserTypeEnum::USER->getEmail())->first();

        if ($categories->isEmpty() || $cities->isEmpty() || !$testUser) {
            $this->command->warn('Categories, cities, or test user not found. Please run their seeders first.');
            return;
        }

        // Simple 5 listings - 1 active, 4 pending
        $listings = [
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'Temiz iPhone 15 Pro, kutusu mevcut.',
                'category' => 'Telefon',
                'price' => 45000,
                'status' => ListingStatusEnum::APPROVED
            ],
            [
                'name' => 'MacBook Air M2',
                'description' => 'Az kullanılmış MacBook Air.',
                'category' => 'Laptop',
                'price' => 35000,
                'status' => ListingStatusEnum::PENDING
            ],
            [
                'name' => '2020 Volkswagen Polo',
                'description' => 'Bakımlı Polo, tek elden.',
                'category' => 'Araba',
                'price' => 450000,
                'status' => ListingStatusEnum::PENDING
            ],
            [
                'name' => 'Yamaha R25',
                'description' => 'Temiz motorsiklet, az kilometreli.',
                'category' => 'Motorsiklet',
                'price' => 85000,
                'status' => ListingStatusEnum::PENDING
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'description' => 'Yeni telefon, garantili.',
                'category' => 'Telefon',
                'price' => 38000,
                'status' => ListingStatusEnum::PENDING
            ],
        ];

        foreach ($listings as $index => $listingData) {
            $category = $categories->firstWhere('name', $listingData['category']);

            if (!$category) {
                $category = $categories->random();
            }

            $city = $cities->random();
            $districts = District::where('city_uuid', $city->uuid)->get();
            $district = $districts->isNotEmpty() ? $districts->random() : District::first();

            $listing = Listing::create([
                'uuid' => \Str::uuid(),
                'unique_number' => Listing::generateUniqueNumber(),
                'name' => $listingData['name'],
                'description' => $listingData['description'],
                'category_uuid' => $category->uuid,
                'city_uuid' => $city->uuid,
                'district_uuid' => $district->uuid,
                'user_uuid' => $testUser->uuid,
                'status' => $listingData['status'],
                'approved_by' => $listingData['status'] === ListingStatusEnum::APPROVED
                    ? $testUser->uuid
                    : null,
                'approved_at' => $listingData['status'] === ListingStatusEnum::APPROVED
                    ? now()->subDays(rand(1, 5))
                    : null,
            ]);
        }

        $this->command->info('Created 5 simple listings (1 active, 4 pending).');
    }
}
