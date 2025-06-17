<?php

namespace App\Models;

use App\User;
use Eloquent;

class Receipt extends Eloquent
{
    protected $fillable = ['pr_id', 'year', 'balance', 'amt_paid', 'branch_id'];

    public function pr()
    {
        return $this->belongsTo(PaymentRecord::class, 'pr_id');
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
