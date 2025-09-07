<?php

namespace App\Services\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseService
{
    protected $repository;
    protected $cacheEnabled = false;
    protected $cacheTime = 3600;

    /**
     * Get all records with pagination
     */
    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->repository->query();
        
        if (!empty($filters)) {
            $query = $this->applyFilters($query, $filters);
        }
        
        return $this->repository->paginateWithQuery($query, $perPage);
    }

    /**
     * Get single record by UUID
     */
    public function getByUuid(string $uuid): ?Model
    {
        if ($this->cacheEnabled) {
            return cache()->remember(
                $this->getCacheKey('uuid', $uuid),
                $this->cacheTime,
                fn() => $this->repository->findByUuid($uuid)
            );
        }
        
        return $this->repository->findByUuid($uuid);
    }

    /**
     * Create new record
     */
    public function create(array $data): Model
    {
        $model = $this->repository->create($data);
        
        if ($this->cacheEnabled) {
            $this->clearCache();
        }
        
        return $model;
    }

    /**
     * Update existing record
     */
    public function update(string $uuid, array $data): ?Model
    {
        $model = $this->repository->update($uuid, $data);
        
        if ($this->cacheEnabled && $model) {
            $this->clearCache();
        }
        
        return $model;
    }

    /**
     * Delete record
     */
    public function delete(string $uuid): bool
    {
        $result = $this->repository->delete($uuid);
        
        if ($this->cacheEnabled && $result) {
            $this->clearCache();
        }
        
        return $result;
    }

    /**
     * Restore soft deleted record
     */
    public function restore(string $uuid): ?Model
    {
        $model = $this->repository->restore($uuid);
        
        if ($this->cacheEnabled && $model) {
            $this->clearCache();
        }
        
        return $model;
    }

    /**
     * Force delete record
     */
    public function forceDelete(string $uuid): bool
    {
        $result = $this->repository->forceDelete($uuid);
        
        if ($this->cacheEnabled && $result) {
            $this->clearCache();
        }
        
        return $result;
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, array $filters)
    {
        return $query;
    }

    /**
     * Get cache key
     */
    protected function getCacheKey(string $type, string $identifier): string
    {
        $className = class_basename($this);
        return sprintf('%s.%s.%s', $className, $type, $identifier);
    }

    /**
     * Clear cache
     */
    protected function clearCache(): void
    {
        cache()->tags([class_basename($this)])->flush();
    }
}