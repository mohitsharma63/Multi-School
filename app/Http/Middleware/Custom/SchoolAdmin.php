<?php

namespace App\Http\Middleware\Custom;

use Closure;
use App\Models\Role;

class SchoolAdmin
{
    public function handle($request, Closure $next)
    {
        if(auth()->user()->role && auth()->user()->role->level <= Role::SCHOOL_ADMIN){
            return $next($request);
        }

        return redirect()->route('home')->with('pop_error', 'Access Denied');
    }
}
