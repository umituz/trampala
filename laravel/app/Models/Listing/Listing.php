<?php

namespace App\Models\Listing;

use App\Enums\Listing\ListingStatusEnum;
use App\Models\Base\BaseUuidModel;
use App\Models\Category\Category;
use App\Models\City\City;
use App\Models\Country\Country;
use App\Models\District\District;
use App\Models\User\User;
use App\Traits\Model\HasApprovalWorkflow;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Listing extends BaseUuidModel implements HasMedia
{
    use InteractsWithMedia, HasApprovalWorkflow;

    protected $approvedStatusValue = ListingStatusEnum::APPROVED;
    protected $pendingStatusValue = ListingStatusEnum::PENDING;
    protected $rejectedStatusValue = ListingStatusEnum::REJECTED;

    protected $fillable = [
        'unique_number',
        'name',
        'description',
        'category_uuid',
        'country_uuid',
        'city_uuid',
        'district_uuid',
        'user_uuid',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'status' => ListingStatusEnum::class,
        'approved_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($listing) {
            if (empty($listing->unique_number)) {
                $listing->unique_number = self::generateUniqueNumber();
            }
        });
    }

    /**
     * Generate unique listing number
     */
    public static function generateUniqueNumber(): string
    {
        do {
            $number = 'LST-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('unique_number', $number)->exists());

        return $number;
    }

    /**
     * Get the category this listing belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_uuid');
    }

    /**
     * Get the country this listing is in
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_uuid');
    }

    /**
     * Get the city this listing is in
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_uuid');
    }

    /**
     * Get the district this listing is in
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_uuid');
    }

    /**
     * Get the user who created this listing
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }

    /**
     * Get the admin who approved this listing
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }


    /**
     * Scope to filter by category
     */
    public function scopeByCategory(Builder $query, string $categoryUuid): Builder
    {
        return $query->where('category_uuid', $categoryUuid);
    }

    /**
     * Scope to filter by country
     */
    public function scopeByCountry(Builder $query, string $countryUuid): Builder
    {
        return $query->where('country_uuid', $countryUuid);
    }

    /**
     * Scope to filter by city
     */
    public function scopeByCity(Builder $query, string $cityUuid): Builder
    {
        return $query->where('city_uuid', $cityUuid);
    }

    /**
     * Scope to filter by district
     */
    public function scopeByDistrict(Builder $query, string $districtUuid): Builder
    {
        return $query->where('district_uuid', $districtUuid);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser(Builder $query, string $userUuid): Builder
    {
        return $query->where('user_uuid', $userUuid);
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->acceptsMimeTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
            ->singleFile(false);
    }

    /**
     * Register media conversions
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);

        $this->addMediaConversion('large')
            ->width(800)
            ->height(600)
            ->sharpen(10);
    }

    /**
     * Get the first image URL for this listing
     */
    public function getImageUrl(): ?string
    {
        $firstImage = $this->getFirstMedia('images');

        if (!$firstImage) {
            \Log::info('No first image found for listing', ['listing_id' => $this->uuid]);
            return $this->getDefaultImageUrl();
        }

        // Ensure absolute URL for external access
        $url = $firstImage->getFullUrl();
        \Log::info('First image URL generated', [
            'listing_id' => $this->uuid,
            'media_id' => $firstImage->id,
            'url' => $url,
            'file_name' => $firstImage->file_name,
            'collection' => $firstImage->collection_name
        ]);

        return $url;
    }

    /**
     * Get all image URLs for this listing
     */
    public function getImageUrls(): array
    {
        return $this->getMedia('images')->map(function ($media) {
            return $media->getFullUrl();
        })->toArray();
    }

    /**
     * Get thumbnail URL for the first image
     */
    public function getThumbnailUrl(): ?string
    {
        $firstImage = $this->getFirstMedia('images');

        if (!$firstImage) {
            return $this->getDefaultImageUrl();
        }

        return $firstImage->getFullUrl('thumb');
    }

    /**
     * Get default image URL
     */
    private function getDefaultImageUrl(): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="640" height="480" viewBox="0 0 640 480">
                <rect width="640" height="480" fill="#f3f4f6"/>
                <g transform="translate(320,240)" text-anchor="middle" dominant-baseline="middle">
                    <circle cx="0" cy="-20" r="30" fill="#d1d5db" stroke="#9ca3af" stroke-width="2"/>
                    <path d="M-15,-10 L15,-10 L10,5 L-10,5 Z" fill="#9ca3af"/>
                    <circle cx="-8" cy="-15" r="3" fill="#6b7280"/>
                    <text y="50" font-family="Arial,sans-serif" font-size="16" fill="#6b7280">No Image</text>
                </g>
            </svg>'
        );
    }
}
