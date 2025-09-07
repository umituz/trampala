<?php

namespace App\Http\Controllers\Admin\Country;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\Country\CountryListRequest;
use App\Http\Resources\Admin\Country\CountryCollection;
use App\Http\Resources\Admin\Country\CountryResource;
use App\Http\Resources\Admin\City\CityCollection;
use App\Models\Country\Country;
use App\Services\Admin\Country\CountryService;
use Illuminate\Http\JsonResponse;

/**
 * REST API Controller for Country management.
 *
 * Handles read operations for countries for location services.
 * Countries are managed through database seeders.
 *
 * @package App\Http\Controllers\Country
 */
class CountryController extends BaseController
{
    /**
     * Create a new CountryController instance.
     *
     * @param CountryService $countryService The country service for country operations
     */
    public function __construct(protected CountryService $countryService) {}

    /**
     * Display a listing of all available countries.
     *
     * @param CountryListRequest $request The validated request for country listing
     * @return JsonResponse JSON response containing country collection
     */
    public function index(CountryListRequest $request): JsonResponse
    {
        $countries = $this->countryService->getAllCountries();

        return $this->ok(new CountryCollection($countries), 'Countries retrieved successfully');
    }

    /**
     * Display the specified country.
     *
     * @param Country $country The country model instance resolved by route model binding
     * @return JsonResponse JSON response containing the country resource
     */
    public function show(Country $country): JsonResponse
    {
        return $this->ok(new CountryResource($country), 'Country retrieved successfully');
    }


    /**
     * Get cities for the specified country.
     *
     * @param Country $country The country model instance resolved by route model binding
     * @return JsonResponse JSON response containing cities collection
     */
    public function cities(Country $country): JsonResponse
    {
        $cities = $country->cities()->active()->orderBy('name')->get();

        return $this->ok(new CityCollection($cities), 'Cities retrieved successfully');
    }

}
