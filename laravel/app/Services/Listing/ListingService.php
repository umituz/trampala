<?php

namespace App\Services\Listing;

use App\DTOs\Listing\ListingCreateDTO;
use App\DTOs\Listing\ListingUpdateDTO;
use App\Enums\Listing\ListingStatusEnum;
use App\Models\Listing\Listing;
use App\Repositories\Listing\ListingRepositoryInterface;
use App\Services\Base\BaseService;
use App\Services\Media\MediaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

class ListingService extends BaseService
{
    public function __construct(
        protected ListingRepositoryInterface $listingRepository,
        protected MediaService $mediaService
    ) {
        $this->repository = $listingRepository;
    }

    /**
     * Get all approved listings with pagination
     */
    public function getApprovedListings(int $perPage = 15): LengthAwarePaginator
    {
        return $this->listingRepository->getApprovedListings($perPage);
    }

    /**
     * Get pending listings for admin approval
     */
    public function getPendingListings(int $perPage = 15): LengthAwarePaginator
    {
        return $this->listingRepository->getPendingListings($perPage);
    }

    /**
     * Get user's own listings
     */
    public function getUserListings(string $userUuid, int $perPage = 15): LengthAwarePaginator
    {
        return $this->listingRepository->getUserListings($userUuid, $perPage);
    }

    /**
     * Create a new listing using DTO
     */
    public function createListing(ListingCreateDTO $dto): Listing
    {
        $data = $dto->toArray();
        $data['status'] = ListingStatusEnum::PENDING;
        $listing = $this->listingRepository->create($data);

        if ($dto->image && $dto->image instanceof UploadedFile) {
            $this->mediaService->handleMedia($listing, $dto->image, 'images', false);
        }

        return $listing;
    }

    /**
     * Update a listing using DTO
     */
    public function updateListing(Listing $listing, ListingUpdateDTO $dto): Listing
    {
        if (!empty($dto->toArray())) {
            $listing = $this->listingRepository->updateByUuid($listing->uuid, $dto->toArray());
        }

        if ($dto->image && $dto->image instanceof \Illuminate\Http\UploadedFile) {
            $this->mediaService->handleMedia($listing, $dto->image, 'images', true);
        }

        return $listing;
    }

    /**
     * Delete a listing (soft delete)
     */
    public function deleteListing(Listing $listing): bool
    {
        return $this->listingRepository->deleteByUuid($listing->uuid);
    }

    /**
     * Restore a soft deleted listing
     */
    public function restoreListing(Listing $listing): bool
    {
        return $this->listingRepository->restoreByUuid($listing);
    }

    /**
     * Force delete a listing permanently
     */
    public function forceDeleteListing(Listing $listing): bool
    {
        $this->mediaService->clearMediaCollection($listing, 'images');

        return $this->listingRepository->forceDeleteByUuid($listing);
    }

    /**
     * Approve a listing
     */
    public function approveListing(Listing $listing, string $approvedBy): Listing
    {
        $listing->approve($approvedBy);

        return $listing->fresh();
    }

    /**
     * Reject a listing
     */
    public function rejectListing(Listing $listing, string $rejectionReason, string $rejectedBy): Listing
    {
        $listing->reject($rejectionReason, $rejectedBy);

        return $listing->fresh();
    }

    /**
     * Get listing details with relationships
     */
    public function getListingDetails(Listing $listing): Listing
    {
        return $listing->load(['category', 'city', 'district', 'user']);
    }


    /**
     * Get listing statistics for admin dashboard
     */
    public function getListingStats(): array
    {
        $total = $this->listingRepository->count();
        $pending = $this->listingRepository->countByStatus(ListingStatusEnum::PENDING);
        $approved = $this->listingRepository->countByStatus(ListingStatusEnum::APPROVED);

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
        ];
    }
}
