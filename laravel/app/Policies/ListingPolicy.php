<?php

namespace App\Policies;

use App\Enums\Listing\ListingStatusEnum;
use App\Models\Listing\Listing;
use App\Models\User\User;

class ListingPolicy
{
    /**
     * Determine whether the user can view any listings.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the listing.
     */
    public function view(?User $user, Listing $listing): bool
    {
        // Anyone can view approved listings
        if ($listing->status === ListingStatusEnum::APPROVED) {
            return true;
        }

        // Only authenticated users can view pending/rejected listings
        if (!$user) {
            return false;
        }

        // Owner and admins can view any status
        return $this->isOwnerOrAdmin($user, $listing);
    }

    /**
     * Determine whether the user can create listings.
     */
    public function create(User $user): bool
    {
        return $user !== null;
    }

    /**
     * Determine whether the user can update the listing.
     */
    public function update(User $user, Listing $listing): bool
    {
        return $this->isOwnerOrAdmin($user, $listing);
    }

    /**
     * Determine whether the user can delete the listing.
     */
    public function delete(User $user, Listing $listing): bool
    {
        return $this->isOwnerOrAdmin($user, $listing);
    }

    /**
     * Determine whether the user can restore the listing.
     */
    public function restore(User $user, Listing $listing): bool
    {
        return $this->isOwnerOrAdmin($user, $listing);
    }

    /**
     * Determine whether the user can permanently delete the listing.
     */
    public function forceDelete(User $user, Listing $listing): bool
    {
        return $user->hasRole(['admin', 'Admin']);
    }

    /**
     * Determine whether the user can approve/reject listings.
     */
    public function moderate(User $user, Listing $listing): bool
    {
        return $user->hasRole(['admin', 'Admin']);
    }

    /**
     * Determine whether the user can view pending listings.
     */
    public function viewPending(User $user): bool
    {
        return $user->hasRole(['admin', 'Admin']);
    }

    /**
     * Check if user is owner or admin
     */
    protected function isOwnerOrAdmin(User $user, Listing $listing): bool
    {
        return $user->uuid === $listing->user_uuid
            || $user->hasRole(['admin', 'Admin']);
    }
}
