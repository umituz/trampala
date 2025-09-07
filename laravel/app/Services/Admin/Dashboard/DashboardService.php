<?php

declare(strict_types=1);

namespace App\Services\Admin\Dashboard;

use App\Repositories\Admin\Dashboard\DashboardRepositoryInterface;
use Carbon\Carbon;

/**
 * Dashboard Service for administrative dashboard operations.
 *
 * Handles comprehensive dashboard data aggregation including system metrics,
 * user statistics, and system status monitoring.
 * Provides business intelligence and real-time dashboard data for administrators.
 *
 * @package App\Services\Dashboard
 */
class DashboardService
{
    /**
     * Create a new DashboardService instance.
     *
     * @param DashboardRepositoryInterface $dashboardRepository The dashboard repository for analytics operations
     */
    public function __construct(protected DashboardRepositoryInterface $dashboardRepository) {}

    /**
     * Get comprehensive dashboard data including stats and activities.
     *
     * @return array Array containing dashboard statistics and recent activities
     */
    public function getDashboardData(): array
    {

        return [
            'stats' => $this->getStats(),
            'recent_activity' => $this->getRecentActivity()
        ];
    }

    /**
     * Generate statistical data comparing current and previous month metrics.
     *
     * @param Carbon $currentMonth The current month start date for metrics calculation
     * @param Carbon $previousMonth The previous month start date for comparison
     * @return array Array containing formatted statistics with percentage changes
     */
    private function getStats(): array
    {
        $totalUsers = $this->dashboardRepository->getTotalUsers();

        return [
            [
                'title' => 'Total Users',
                'value' => number_format($totalUsers),
                'description' => 'Registered users'
            ]
        ];
    }

    /**
     * Retrieve and format recent system activities including users.
     *
     * @return array Array of recent activities sorted by timestamp
     */
    private function getRecentActivity(): array
    {
        $userRegistrations = $this->dashboardRepository->getRecentUserRegistrations();

        $activities = collect();

        foreach ($userRegistrations as $user) {
            $activities->push([
                'uuid' => 'user_' . $user->uuid,
                'type' => 'user',
                'message' => $user->name . ' joined Trampala',
                'timestamp' => $user->created_at->diffForHumans(),
                'user' => $user->name,
                'status' => 'info'
            ]);
        }

        return $activities->take(8)->values()->toArray();
    }





}
