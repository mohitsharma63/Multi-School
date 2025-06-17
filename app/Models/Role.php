<?php

namespace App\Models;

use Eloquent;

class Role extends Eloquent
{
    protected $fillable = [
        'name', 'display_name', 'description', 'level', 'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'level' => 'integer'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    // Role level constants
    const SUPER_ADMIN = 1;
    const SCHOOL_ADMIN = 2;
    const BRANCH_ADMIN = 3;
    const STAFF = 4;
    const PARENT_STUDENT = 5;
}
