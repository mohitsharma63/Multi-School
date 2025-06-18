<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'system_title',
        'current_session',
        'term_begins',
        'term_ends',
        'logo',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, Branch::class);
    }
}
