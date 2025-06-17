<?php

namespace App;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = ['title', 'author', 'isbn', 'quantity', 'branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
