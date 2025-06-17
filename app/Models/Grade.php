<?php

namespace App\Models;

use Eloquent;

class Grade extends Eloquent
{
    protected $fillable = ['name', 'class_type_id', 'mark_from', 'mark_to', 'remark', 'branch_id'];

    public function class_type()
    {
        return $this->belongsTo(ClassType::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
