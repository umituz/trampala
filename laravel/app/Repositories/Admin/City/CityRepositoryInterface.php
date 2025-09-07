<?php

namespace App\Repositories\Admin\City;

use App\Models\City\City;
use App\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface CityRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all active cities
     */
    public function getAllActive(): Collection;

    /**
     * Get city with its districts
     */
    public function getCityWithDistricts(City $city): City;
}
