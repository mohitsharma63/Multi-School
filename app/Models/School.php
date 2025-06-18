<?php

namespace App\Models;

use Eloquent;

class School extends Eloquent
{
    protected $fillable = [
        'name', 'acronym', 'email', 'phone', 'address', 'logo',
        'current_session', 'term_ends', 'term_begins', 'lock_exam', 'is_active'
    ];

    protected $casts = [
        'lock_exam' => 'boolean',
        'is_active' => 'boolean',
        'term_ends' => 'date',
        'term_begins' => 'date',
    ];
}
