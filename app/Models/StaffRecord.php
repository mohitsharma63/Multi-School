<?php

namespace App\Models;

use App\User;
use Eloquent;

class StaffRecord extends Eloquent
{
    protected $fillable = ['code', 'emp_date', 'user_id', 'branch_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
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
