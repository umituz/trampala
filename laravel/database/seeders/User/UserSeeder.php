<?php

namespace Database\Seeders\User;

use App\Enums\User\UserTypeEnum;
use App\Models\Country\Country;
use App\Models\Language\Language;
use App\Models\User\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->createAdminUser();
        $this->createRegularUser();
    }

    private function createAdminUser(): void
    {
        $defaultLanguage = Language::where('code', 'en')->first();
        $turkeyCountry = Country::where('code', 'TR')->first();

        if (!$turkeyCountry) {
            throw new \Exception('Turkey country not found. Please run CountrySeeder first.');
        }

        $admin = User::firstOrCreate(
            ['email' => UserTypeEnum::ADMIN->getEmail()],
            [
                'name' => UserTypeEnum::ADMIN->getLabel(),
                'password' => 'admin123',
                'language_uuid' => $defaultLanguage->uuid,
                'country_uuid' => $turkeyCountry->uuid,
                'terms_accepted' => true,
                'terms_accepted_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('Admin');
    }

    private function createRegularUser(): void
    {
        $defaultLanguage = Language::where('code', 'en')->first();
        $turkeyCountry = Country::where('code', 'TR')->first();

        if (!$turkeyCountry) {
            throw new \Exception('Turkey country not found. Please run CountrySeeder first.');
        }

        $user = User::firstOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'Test User',
                'password' => 'user123',
                'language_uuid' => $defaultLanguage->uuid,
                'country_uuid' => $turkeyCountry->uuid,
                'terms_accepted' => true,
                'terms_accepted_at' => now(),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('User');
    }
}
