<?php

namespace App\Traits\Repository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

trait HasPagination
{
    /**
     * Get paginated results with optional relations
     */
    public function paginateWithRelations(
        array $relations = [],
        int $perPage = 15,
        array $columns = ['*'],
        string $orderBy = 'created_at',
        string $orderDirection = 'desc'
    ): LengthAwarePaginator {
        return $this->model
            ->with($relations)
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage, $columns);
    }

    /**
     * Get paginated results with query builder
     */
    public function paginateWithQuery(
        Builder $query,
        int $perPage = 15,
        array $columns = ['*']
    ): LengthAwarePaginator {
        return $query->paginate($perPage, $columns);
    }

    /**
     * Get paginated active records
     */
    public function paginateActive(
        int $perPage = 15,
        array $relations = [],
        string $statusField = 'status',
        $activeValue = 1
    ): LengthAwarePaginator {
        return $this->model
            ->where($statusField, $activeValue)
            ->with($relations)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}