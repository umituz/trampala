<?php

namespace Database\Seeders;

use App\Models\City\City;
use App\Models\Country\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Turkey country UUID
        $turkeyCountry = Country::where('code', 'TR')->first();
        
        if (!$turkeyCountry) {
            throw new \Exception('Turkey country not found. Please run CountrySeeder first.');
        }

        // Major Turkish cities
        $cities = [
            ['name' => 'Istanbul', 'plate_code' => '34'],
            ['name' => 'Ankara', 'plate_code' => '06'],
            ['name' => 'Izmir', 'plate_code' => '35'],
            ['name' => 'Bursa', 'plate_code' => '16'],
            ['name' => 'Antalya', 'plate_code' => '07'],
            ['name' => 'Adana', 'plate_code' => '01'],
            ['name' => 'Konya', 'plate_code' => '42'],
            ['name' => 'Gaziantep', 'plate_code' => '27'],
            ['name' => 'Kayseri', 'plate_code' => '38'],
            ['name' => 'Mersin', 'plate_code' => '33'],
            ['name' => 'Diyarbakir', 'plate_code' => '21'],
            ['name' => 'Eskisehir', 'plate_code' => '26'],
            ['name' => 'Sanliurfa', 'plate_code' => '63'],
            ['name' => 'Trabzon', 'plate_code' => '61'],
            ['name' => 'Malatya', 'plate_code' => '44'],
            ['name' => 'Denizli', 'plate_code' => '20'],
            ['name' => 'Sakarya', 'plate_code' => '54'],
            ['name' => 'Samsun', 'plate_code' => '55'],
            ['name' => 'Van', 'plate_code' => '65'],
            ['name' => 'Kocaeli', 'plate_code' => '41'],
        ];

        foreach ($cities as $cityData) {
            City::create([
                'country_uuid' => $turkeyCountry->uuid,
                'name' => $cityData['name'],
                'plate_code' => $cityData['plate_code'],
                'status' => 1,
            ]);
        }
    }
}
