<?php

namespace App\Models\District;

use App\Models\Base\BaseUuidModel;
use App\Models\City\City;
use App\Models\Listing\Listing;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends BaseUuidModel
{
    protected $fillable = [
        'name',
        'city_uuid',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the city this district belongs to
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_uuid');
    }

    /**
     * Get listings in this district
     */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'district_uuid');
    }

    /**
     * Check if district is active
     */
    public function isActive(): bool
    {
        return $this->status === 1;
    }

    /**
     * Scope to get only active districts
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to get districts by city
     */
    public function scopeByCity($query, string $cityUuid)
    {
        return $query->where('city_uuid', $cityUuid);
    }
}