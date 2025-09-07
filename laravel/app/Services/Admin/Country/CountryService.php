<?php

namespace App\Services\Admin\Country;

use App\Models\Country\Country;
use App\Repositories\Admin\Country\CountryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Country Service for country management operations.
 *
 * Handles read operations for countries for location services.
 * Countries are managed through database seeders.
 *
 * @package App\Services\Country
 */
class CountryService
{
    /**
     * Create a new CountryService instance.
     *
     * @param CountryRepositoryInterface $countryRepository The country repository for data operations
     */
    public function __construct(protected CountryRepositoryInterface $countryRepository) {}

    /**
     * Get all available countries.
     *
     * @return Collection Collection of all countries in the system
     */
    public function getAllCountries(): Collection
    {
        return $this->countryRepository->all();
    }


    /**
     * Find a country by its country code.
     *
     * @param string $code The country code to search for (e.g., 'US', 'TR')
     * @return Country The country instance matching the code
     * @throws \InvalidArgumentException When country code is invalid or not found
     */
    public function findByCode(string $code): Country
    {
        $country = $this->countryRepository->findByCode($code);

        if (!$country) {
            throw new \InvalidArgumentException('Invalid country code provided');
        }

        return $country;
    }

}
