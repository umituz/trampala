<?php

namespace Database\Seeders;

use Database\Seeders\Authority\PermissionSeeder;
use Database\Seeders\Authority\RolePermissionSeeder;
use Database\Seeders\Authority\RoleSeeder;
use Database\Seeders\Country\CountrySeeder;
use Database\Seeders\Currency\CurrencySeeder;
use Database\Seeders\Language\LanguageSeeder;
use Database\Seeders\User\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            LanguageSeeder::class,
            CurrencySeeder::class,
            CountrySeeder::class,
            UserSeeder::class,

            // Listing system seeders
            CategorySeeder::class,
            CitySeeder::class,
            DistrictSeeder::class,
            #ListingSeeder::class,
        ]);
    }
}
