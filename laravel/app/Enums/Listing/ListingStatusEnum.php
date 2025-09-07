<?php

namespace App\Enums\Listing;

enum ListingStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    /**
     * Get human-readable description of the enum value
     */
    public function description(): string
    {
        return match($this) {
            self::PENDING => 'Pending Approval',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        };
    }

    /**
     * Get all enum values as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all enum descriptions as an array
     */
    public static function descriptions(): array
    {
        return array_map(
            fn (self $case) => $case->description(),
            self::cases()
        );
    }

    /**
     * Get all enum values with their descriptions as an associative array
     */
    public static function options(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_map(fn (self $case) => $case->description(), self::cases())
        );
    }

    /**
     * Check if the enum contains a specific value
     */
    public static function contains(string $value): bool
    {
        return in_array($value, self::values(), true);
    }

    /**
     * Get a case from a value or return null
     */
    public static function tryFromValue(string $value): ?self
    {
        return self::tryFrom($value);
    }

    /**
     * Check if listing is approved
     */
    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    /**
     * Check if listing is pending
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if listing is rejected
     */
    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }
}
