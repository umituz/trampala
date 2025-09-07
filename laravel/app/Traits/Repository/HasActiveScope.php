<?php

namespace App\Traits\Repository;

use Illuminate\Database\Eloquent\Collection;

trait HasActiveScope
{
    /**
     * Get only active records
     */
    public function getActive(
        string $statusField = 'status',
        $activeValue = 1,
        array $relations = []
    ): Collection {
        return $this->model
            ->where($statusField, $activeValue)
            ->with($relations)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get active records with custom ordering
     */
    public function getActiveOrdered(
        string $orderBy = 'name',
        string $orderDirection = 'asc',
        string $statusField = 'status',
        $activeValue = 1
    ): Collection {
        return $this->model
            ->where($statusField, $activeValue)
            ->orderBy($orderBy, $orderDirection)
            ->get();
    }

    /**
     * Count active records
     */
    public function countActive(
        string $statusField = 'status',
        $activeValue = 1
    ): int {
        return $this->model
            ->where($statusField, $activeValue)
            ->count();
    }
}