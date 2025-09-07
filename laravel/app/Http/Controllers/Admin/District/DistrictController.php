<?php

namespace App\Http\Controllers\Admin\District;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Admin\District\DistrictCollection;
use App\Http\Resources\Admin\District\DistrictResource;
use App\Models\District\District;
use App\Models\City\City;
use Illuminate\Http\JsonResponse;

/**
 * REST API Controller for District management.
 *
 * Handles read operations for districts for location services.
 * Districts are managed through database seeders.
 *
 * @package App\Http\Controllers\District
 */
class DistrictController extends BaseController
{
    /**
     * Display a listing of districts for a specific city.
     *
     * @param City $city The city model instance resolved by route model binding
     * @return JsonResponse JSON response containing district collection
     */
    public function index(City $city): JsonResponse
    {
        $districts = $city->districts()->active()->orderBy('name')->get();

        return $this->ok(new DistrictCollection($districts), 'Districts retrieved successfully');
    }

    /**
     * Display the specified district.
     *
     * @param District $district The district model instance resolved by route model binding
     * @return JsonResponse JSON response containing the district resource
     */
    public function show(District $district): JsonResponse
    {
        return $this->ok(new DistrictResource($district), 'District retrieved successfully');
    }
}