<?php

namespace App\Repositories\Listing;

use App\Repositories\Base\BaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface ListingRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all listings with pagination
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get approved listings only
     */
    public function getApprovedListings(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get pending listings for admin approval
     */
    public function getPendingListings(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get rejected listings
     */
    public function getRejectedListings(int $perPage = 15): LengthAwarePaginator;


    /**
     * Get user's listings
     */
    public function getUserListings(string $userUuid, int $perPage = 15): LengthAwarePaginator;

    /**
     * Count by status
     */
    public function countByStatus(\App\Enums\Listing\ListingStatusEnum $status): int;
}
