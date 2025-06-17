<?php

namespace App;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;

class BookRequest extends Model
{
    protected $fillable = ['user_id', 'book_id', 'request_date', 'status', 'branch_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
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
