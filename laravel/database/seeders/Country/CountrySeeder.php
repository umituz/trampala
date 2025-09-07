<?php

namespace Database\Seeders\Country;

use App\Enums\Country\CountryEnum;
use App\Models\Country\Country;
use App\Models\Currency\Currency;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        // Only Turkey for this application
        $turkeyCountry = CountryEnum::TURKEY;
        $currency = Currency::where('code', $turkeyCountry->getCurrencyCode())->first();
        
        Country::updateOrCreate(
            ['code' => $turkeyCountry->value],
            [
                'code' => $turkeyCountry->value,
                'name' => $turkeyCountry->getDisplayName(),
                'locale' => $turkeyCountry->getLocale(),
                'currency_uuid' => $currency?->uuid,
                'is_active' => true
            ]
        );

        $this->command->info('Turkey country seeded successfully.');
    }
}