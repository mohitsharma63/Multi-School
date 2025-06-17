<?php

namespace App\Models;

use Eloquent;

class Setting extends Eloquent
{
    protected $fillable = ['type', 'description', 'branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('branch_id');
    }
}
