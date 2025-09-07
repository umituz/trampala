<?php

namespace App\Services\Admin\City;

use App\Models\City\City;
use App\Repositories\Admin\City\CityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CityService
{
    public function __construct(protected CityRepositoryInterface $cityRepository) {}

    /**
     * Get all active cities
     */
    public function getAllCities(): Collection
    {
        return $this->cityRepository->getAllActive();
    }

    /**
     * Get city with its districts
     */
    public function getCityWithDistricts(City $city): City
    {
        return $this->cityRepository->getCityWithDistricts($city);
    }
}
