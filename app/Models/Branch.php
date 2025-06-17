
<?php

namespace App\Models;

use Eloquent;

class Branch extends Eloquent
{
    protected $fillable = [
        'school_id', 'name', 'code', 'location', 'phone', 'email', 'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function students()
    {
        return $this->hasMany(User::class)->where('user_type', 'student');
    }

    public function teachers()
    {
        return $this->hasMany(User::class)->where('user_type', 'teacher');
    }

    public function parents()
    {
        return $this->hasMany(User::class)->where('user_type', 'parent');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
