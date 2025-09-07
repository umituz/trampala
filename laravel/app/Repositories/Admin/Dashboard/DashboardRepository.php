<?php

declare(strict_types=1);

namespace App\Repositories\Admin\Dashboard;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository class for managing dashboard data operations.
 *
 * This repository provides methods for retrieving dashboard statistics
 * including user counts and system status information.
 *
 * @package App\Repositories\Dashboard
 */
class DashboardRepository implements DashboardRepositoryInterface
{
    /**
     * Get the total number of users in the system.
     *
     * @return int The total count of all users
     */
    public function getTotalUsers(): int
    {
        return User::count();
    }


    /**
     * Get the most recently registered users.
     *
     * @param int $limit The maximum number of users to retrieve (default: 3)
     * @return Collection Collection of recently registered users
     */
    public function getRecentUserRegistrations(int $limit = 3): Collection
    {
        return User::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }



}
