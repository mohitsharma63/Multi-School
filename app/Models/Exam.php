<?php

namespace App\Models;

use Eloquent;

class Exam extends Eloquent
{
    protected $fillable = ['name', 'term', 'year', 'branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
