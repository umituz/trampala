<?php

namespace App\Http\Controllers\Admin\City;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Admin\City\CityResource;
use App\Models\City\City;
use App\Services\Admin\City\CityService;
use Illuminate\Http\JsonResponse;

class CityController extends BaseController
{
    public function __construct(protected CityService $cityService) {}

    /**
     * Display a listing of cities
     */
    public function index(): JsonResponse
    {
        $cities = $this->cityService->getAllCities();

        return $this->ok(
            $cities->map(fn($city) => new CityResource($city)),
            'Cities retrieved successfully.'
        );
    }

    /**
     * Display the specified city with districts
     */
    public function show(City $city): JsonResponse
    {
        $city = $this->cityService->getCityWithDistricts($city);

        return $this->ok(
            new CityResource($city),
            'City retrieved successfully.'
        );
    }
}
