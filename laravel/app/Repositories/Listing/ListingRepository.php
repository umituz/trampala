<?php

namespace App\Repositories\Listing;

use App\Enums\Listing\ListingStatusEnum;
use App\Models\Listing\Listing;
use App\Repositories\Base\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ListingRepository extends BaseRepository implements ListingRepositoryInterface
{
    public function __construct(Listing $model)
    {
        parent::__construct($model);
    }

    /**
     * Count by status
     */
    public function countByStatus(ListingStatusEnum $status): int
    {
        return $this->model->where('status', $status->value)->count();
    }

    /**
     * Get all listings with pagination
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['category', 'city', 'district', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get approved listings only
     */
    public function getApprovedListings(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->approved()
            ->with(['category', 'city', 'district', 'user'])
            ->orderBy('approved_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get pending listings for admin approval
     */
    public function getPendingListings(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->pending()
            ->with(['category', 'city', 'district', 'user'])
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
    }

    /**
     * Get rejected listings
     */
    public function getRejectedListings(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->rejected()
            ->with(['category', 'city', 'district', 'user', 'approvedBy'])
            ->orderBy('approved_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get user's listings
     */
    public function getUserListings(string $userUuid, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->byUser($userUuid)
            ->with(['category', 'city', 'district'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

}
