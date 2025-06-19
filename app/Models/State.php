<?php

namespace App\Models;

use Eloquent;

class State extends Eloquent
{
    public function lgas()
    {
        return $this->hasMany(Lga::class);
    }
}
