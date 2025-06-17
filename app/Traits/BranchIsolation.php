<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BranchIsolation
{
    /**
     * Boot the trait
     */
    protected static function bootBranchIsolation()
    {
        // Auto-assign branch_id when creating records
        static::creating(function ($model) {
            $user = auth()->user();

            if ($user && !$user->isSuperAdmin() && $user->branch_id) {
                $model->branch_id = $user->branch_id;
            }
        });

        // Apply global scope for non-super admins
        static::addGlobalScope('branch_isolation', function (Builder $builder) {
            $user = auth()->user();

            if ($user && !$user->isSuperAdmin() && $user->branch_id) {
                $builder->where('branch_id', $user->branch_id);
            }
        });
    }

    /**
     * Scope to filter by specific branch
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to get records without branch isolation (for super admins)
     */
    public function scopeWithoutBranchIsolation($query)
    {
        return $query->withoutGlobalScope('branch_isolation');
    }

    /**
     * Check if current user can access this record
     */
    public function canAccess()
    {
        $user = auth()->user();

        if (!$user) return false;
        if ($user->isSuperAdmin()) return true;

        return $this->branch_id === $user->branch_id;
    }
}
