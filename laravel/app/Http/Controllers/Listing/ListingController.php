<?php

namespace App\Http\Controllers\Listing;

use App\DTOs\Listing\ListingCreateDTO;
use App\DTOs\Listing\ListingUpdateDTO;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Listing\ListingCreateRequest;
use App\Http\Requests\Listing\ListingListRequest;
use App\Http\Requests\Listing\ListingUpdateRequest;
use App\Http\Resources\Listing\ListingResource;
use App\Http\Resources\Listing\ListingCollection;
use App\Models\Listing\Listing;
use App\Services\Listing\ListingService;
use Illuminate\Http\JsonResponse;
use Exception;

class ListingController extends BaseController
{
    public function __construct(protected ListingService $listingService) {}

    /**
     * Display a listing of approved listings (public endpoint)
     */
    public function index(ListingListRequest $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $listings = $this->listingService->getApprovedListings($perPage);

        return $this->ok(new ListingCollection($listings), 'Listings retrieved successfully.');
    }

    /**
     * Store a newly created listing using DTO
     */
    public function store(ListingCreateRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $image = $request->file('image');
            $dto = ListingCreateDTO::fromRequest($data, $image, auth()->user()->uuid);
            $listing = $this->listingService->createListing($dto);

            return $this->created(new ListingResource($listing), 'Listing created successfully.');
        } catch (Exception $e) {
            return $this->error([$e->getMessage()], 'Failed to create listing.');
        }
    }

    /**
     * Display the specified listing
     */
    public function show(Listing $listing): JsonResponse
    {
        $listing = $this->listingService->getListingDetails($listing);

        return $this->ok(new ListingResource($listing), 'Listing retrieved successfully.');
    }

    /**
     * Update the specified listing using DTO
     */
    public function update(ListingUpdateRequest $request, Listing $listing): JsonResponse
    {
        try {
            $data = $request->validated();
            $image = $request->file('image');
            $dto = ListingUpdateDTO::fromRequest($data, $image);
            $updatedListing = $this->listingService->updateListing($listing, $dto);

            return $this->ok(
                new ListingResource($updatedListing),
                'Listing updated successfully.'
            );
        } catch (Exception $e) {
            return $this->error(
                [$e->getMessage()],
                'Failed to update listing.'
            );
        }
    }

    /**
     * Remove the specified listing (soft delete)
     */
    public function destroy(Listing $listing): JsonResponse
    {
        try {
            $this->listingService->deleteListing($listing);

            return $this->ok(
                null,
                'Listing deleted successfully.'
            );
        } catch (Exception $e) {
            return $this->error(
                [$e->getMessage()],
                'Failed to delete listing.'
            );
        }
    }

    /**
     * Restore a soft deleted listing
     */
    public function restore(Listing $listing): JsonResponse
    {
        try {
            $success = $this->listingService->restoreListing($listing);

            if (!$success) {
                return $this->error(
                    ['Failed to restore listing'],
                    'Failed to restore listing.'
                );
            }

            return $this->ok(
                null,
                'Listing restored successfully.'
            );
        } catch (Exception $e) {
            return $this->error(
                [$e->getMessage()],
                'Failed to restore listing.'
            );
        }
    }

    /**
     * Permanently delete a listing
     */
    public function forceDelete(Listing $listing): JsonResponse
    {
        try {
            $success = $this->listingService->forceDeleteListing($listing);

            if (!$success) {
                return $this->error(
                    ['Failed to permanently delete listing'],
                    'Failed to permanently delete listing.'
                );
            }

            return $this->ok(
                null,
                'Listing permanently deleted successfully.'
            );
        } catch (Exception $e) {
            return $this->error(
                [$e->getMessage()],
                'Failed to permanently delete listing.'
            );
        }
    }

    /**
     * Approve a pending listing (admin only)
     */
    public function approve(Listing $listing): JsonResponse
    {
        try {
            $approvedListing = $this->listingService->approveListing($listing, auth()->user()->uuid);

            return $this->ok(
                new ListingResource($approvedListing),
                'Listing approved successfully.'
            );
        } catch (Exception $e) {
            return $this->error(
                [$e->getMessage()],
                'Failed to approve listing.'
            );
        }
    }

    /**
     * Reject a pending listing (admin only)
     */
    public function reject(Listing $listing): JsonResponse
    {
        try {
            $rejectedListing = $this->listingService->rejectListing(
                $listing,
                request('reason', 'Inappropriate content'),
                auth()->user()->uuid
            );

            return $this->ok(
                new ListingResource($rejectedListing),
                'Listing rejected successfully.'
            );
        } catch (Exception $e) {
            return $this->error(
                [$e->getMessage()],
                'Failed to reject listing.'
            );
        }
    }

    /**
     * Get all pending listings for admin approval
     */
    public function pending(): JsonResponse
    {
        $pendingListings = $this->listingService->getPendingListings();

        return $this->ok(
            new ListingCollection($pendingListings),
            'Pending listings retrieved successfully.'
        );
    }

    /**
     * Get user's own listings
     */
    public function my(): JsonResponse
    {
        $userListings = $this->listingService->getUserListings(auth()->id());

        return $this->ok(
            new ListingCollection($userListings),
            'User listings retrieved successfully.'
        );
    }

    /**
     * Get listing statistics for admin dashboard
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->listingService->getListingStats();

            return $this->ok(
                $stats,
                'Listing statistics retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->error(
                [$e->getMessage()],
                'Failed to retrieve listing statistics.'
            );
        }
    }

}
