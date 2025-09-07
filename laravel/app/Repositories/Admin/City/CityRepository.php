<?php

namespace App\Repositories\Admin\City;

use App\Models\City\City;
use App\Repositories\Base\BaseRepository;
use App\Traits\Repository\HasActiveScope;
use Illuminate\Database\Eloquent\Collection;

class CityRepository extends BaseRepository implements CityRepositoryInterface
{
    use HasActiveScope;
    
    public function __construct(City $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all active cities
     */
    public function getAllActive(): Collection
    {
        return $this->getActiveOrdered('name', 'asc');
    }

    /**
     * Get city with its districts
     */
    public function getCityWithDistricts(City $city): City
    {
        return $city->load(['districts' => function ($query) {
            $query->where('status', 1)->orderBy('name');
        }]);
    }
}
