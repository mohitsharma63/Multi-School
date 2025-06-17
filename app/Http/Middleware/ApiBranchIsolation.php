<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiBranchIsolation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate branch access if branch_id is provided in request
        if ($request->has('branch_id') && !$user->canAccessBranch($request->branch_id)) {
            return response()->json(['error' => 'Access denied to this branch'], 403);
        }

        // Validate school access if school_id is provided in request
        if ($request->has('school_id') && !$user->canAccessSchool($request->school_id)) {
            return response()->json(['error' => 'Access denied to this school'], 403);
        }

        // For non-super admin users, force their branch/school context
        if (!$user->isSuperAdmin()) {
            $request->merge([
                'branch_id' => $user->branch_id,
                'school_id' => $user->school_id
            ]);
        }

        return $next($request);
    }
}
