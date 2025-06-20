<?php

namespace App\Models;

use Eloquent;

class MyClass extends Eloquent
{
    protected $fillable = ['name', 'class_type_id', 'school_id'];

    public function section()
    {
        return $this->hasMany(Section::class);
    }

    public function class_type()
    {
        return $this->belongsTo(ClassType::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student_record()
    {
        return $this->hasMany(StudentRecord::class);
    }
}
