<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

class Dorm extends Model
{
    protected $fillable = ['name', 'description', 'school_id'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
