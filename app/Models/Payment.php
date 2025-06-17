<?php

namespace App\Models;

use Eloquent;

class Payment extends Eloquent
{
    protected $fillable = ['title', 'amount', 'my_class_id', 'description', 'year', 'ref_no', 'branch_id'];

    public function my_class()
    {
        return $this->belongsTo(MyClass::class);
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
