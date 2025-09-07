<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;

abstract class BaseController extends Controller
{
    use ApiResponse;
    
    protected $service;
    protected $resource;
    protected $collection;
    
    /**
     * Standard validation rules for common operations
     */
    protected function getCommonRules(): array
    {
        return [
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
            'sort' => 'sometimes|string',
            'direction' => 'sometimes|in:asc,desc',
            'search' => 'sometimes|string|max:255',
            'include' => 'sometimes|string',
        ];
    }

    /**
     * Apply resource transformation
     */
    protected function transformResource($data, bool $isCollection = false)
    {
        if (!$this->resource && !$this->collection) {
            return $data;
        }
        
        if ($isCollection && $this->collection) {
            return new $this->collection($data);
        }
        
        if (!$isCollection && $this->resource) {
            return new $this->resource($data);
        }
        
        return $data;
    }

    /**
     * Handle service exceptions
     */
    protected function handleServiceException(\Exception $e): \Illuminate\Http\JsonResponse
    {
        \Log::error('Service error: ' . $e->getMessage(), [
            'controller' => static::class,
            'trace' => $e->getTraceAsString()
        ]);
        
        return $this->errorResponse(
            message: 'An error occurred while processing your request',
            code: 500
        );
    }
}