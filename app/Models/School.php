<?php

namespace App\Models;

use Eloquent;

class School extends Eloquent
{
    protected $fillable = [
        'name', 'logo', 'address', 'academic_year', 'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function activeBranches()
    {
        return $this->hasMany(Branch::class)->where('status', 1);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
