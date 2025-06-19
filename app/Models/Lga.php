<?php

namespace App\Models;

use Eloquent;

class Lga extends Eloquent
{
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
