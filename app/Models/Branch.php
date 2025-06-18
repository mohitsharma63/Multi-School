<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'head_name',
        'head_phone',
        'head_email',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function students()
    {
        return $this->hasMany(StudentRecord::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
