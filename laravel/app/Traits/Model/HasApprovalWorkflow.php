<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;

trait HasApprovalWorkflow
{
    /**
     * Scope for approved records
     */
    public function scopeApproved(Builder $query): Builder
    {
        $statusColumn = $this->getApprovalStatusColumn();
        $approvedStatus = $this->getApprovedStatus();
        
        return $query->where($statusColumn, $approvedStatus);
    }

    /**
     * Scope for pending records
     */
    public function scopePending(Builder $query): Builder
    {
        $statusColumn = $this->getApprovalStatusColumn();
        $pendingStatus = $this->getPendingStatus();
        
        return $query->where($statusColumn, $pendingStatus);
    }

    /**
     * Scope for rejected records
     */
    public function scopeRejected(Builder $query): Builder
    {
        $statusColumn = $this->getApprovalStatusColumn();
        $rejectedStatus = $this->getRejectedStatus();
        
        return $query->where($statusColumn, $rejectedStatus);
    }

    /**
     * Check if approved
     */
    public function isApproved(): bool
    {
        $statusColumn = $this->getApprovalStatusColumn();
        $approvedStatus = $this->getApprovedStatus();
        
        return $this->{$statusColumn} === $approvedStatus;
    }

    /**
     * Check if pending
     */
    public function isPending(): bool
    {
        $statusColumn = $this->getApprovalStatusColumn();
        $pendingStatus = $this->getPendingStatus();
        
        return $this->{$statusColumn} === $pendingStatus;
    }

    /**
     * Check if rejected
     */
    public function isRejected(): bool
    {
        $statusColumn = $this->getApprovalStatusColumn();
        $rejectedStatus = $this->getRejectedStatus();
        
        return $this->{$statusColumn} === $rejectedStatus;
    }

    /**
     * Approve the record
     */
    public function approve(?string $approvedBy = null): bool
    {
        $statusColumn = $this->getApprovalStatusColumn();
        $approvedStatus = $this->getApprovedStatus();
        
        $data = [
            $statusColumn => $approvedStatus,
            'approved_at' => now(),
            'rejection_reason' => null,
        ];
        
        if ($approvedBy) {
            $data['approved_by'] = $approvedBy;
        }
        
        return $this->update($data);
    }

    /**
     * Reject the record
     */
    public function reject(?string $reason = null, ?string $rejectedBy = null): bool
    {
        $statusColumn = $this->getApprovalStatusColumn();
        $rejectedStatus = $this->getRejectedStatus();
        
        $data = [
            $statusColumn => $rejectedStatus,
            'rejected_at' => now(),
            'approved_at' => null,
        ];
        
        if ($reason) {
            $data['rejection_reason'] = $reason;
        }
        
        if ($rejectedBy) {
            $data['rejected_by'] = $rejectedBy;
        }
        
        return $this->update($data);
    }

    /**
     * Get approval status column name
     */
    protected function getApprovalStatusColumn(): string
    {
        return property_exists($this, 'approvalStatusColumn') 
            ? $this->approvalStatusColumn 
            : 'status';
    }

    /**
     * Get approved status value
     */
    protected function getApprovedStatus()
    {
        return property_exists($this, 'approvedStatusValue')
            ? $this->approvedStatusValue
            : 'approved';
    }

    /**
     * Get pending status value
     */
    protected function getPendingStatus()
    {
        return property_exists($this, 'pendingStatusValue')
            ? $this->pendingStatusValue
            : 'pending';
    }

    /**
     * Get rejected status value
     */
    protected function getRejectedStatus()
    {
        return property_exists($this, 'rejectedStatusValue')
            ? $this->rejectedStatusValue
            : 'rejected';
    }
}