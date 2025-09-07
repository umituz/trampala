<?php

declare(strict_types=1);

namespace App\Repositories\Admin\Dashboard;

use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for Dashboard repository implementations.
 *
 * Defines the required methods for Dashboard data access layer operations
 * including statistical data retrieval, analytics queries, performance metrics,
 * and system status monitoring functionality.
 *
 * @package App\Repositories\Dashboard
 */
interface DashboardRepositoryInterface
{
    /**
     * Get the total number of users in the system.
     *
     * @return int Total user count
     */
    public function getTotalUsers(): int;


    /**
     * Get recent user registrations for dashboard display.
     *
     * @param int $limit Maximum number of registrations to retrieve
     * @return Collection<int, mixed> Collection of recent user registrations
     */
    public function getRecentUserRegistrations(int $limit = 3): Collection;


}
