<?php

namespace App\Models\City;

use App\Models\Base\BaseUuidModel;
use App\Models\Country\Country;
use App\Models\District\District;
use App\Models\Listing\Listing;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends BaseUuidModel
{
    protected $fillable = [
        'country_uuid',
        'name',
        'plate_code',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get country this city belongs to
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_uuid');
    }

    /**
     * Get districts in this city
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'city_uuid');
    }

    /**
     * Get listings in this city
     */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'city_uuid');
    }

    /**
     * Check if city is active
     */
    public function isActive(): bool
    {
        return $this->status === 1;
    }

    /**
     * Scope to get only active cities
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to get cities with districts
     */
    public function scopeWithDistricts($query)
    {
        return $query->has('districts');
    }
}