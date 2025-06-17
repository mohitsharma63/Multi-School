<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BranchIsolation
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
        $user = auth()->user();

        if ($user && !$user->isSuperAdmin()) {
            // Apply global scope for branch isolation
            $this->applyBranchScopes($user);
        }

        return $next($request);
    }

    /**
     * Apply branch scopes to models
     */
    private function applyBranchScopes($user)
    {
        $branchId = $user->branch_id;

        // Only apply scopes if user has a specific branch
        if ($branchId) {
            $models = [
                'App\Models\StudentRecord',
                'App\Models\Payment',
                'App\Models\StaffRecord',
                'App\Book',
                'App\BookRequest',
                'App\Models\Exam',
                'App\Models\ExamRecord',
                'App\Models\TimeTable',
                'App\Models\TimeSlot',
                'App\Models\MyClass',
                'App\Models\Section',
                'App\Models\Subject',
                'App\Models\Mark',
                'App\Models\Grade',
                'App\Models\Receipt',
                'App\Models\Promotion',
                'App\Models\Setting'
            ];

            foreach ($models as $model) {
                if (class_exists($model)) {
                    $model::addGlobalScope('branch', function ($builder) use ($branchId) {
                        if (Schema::hasColumn($builder->getModel()->getTable(), 'branch_id')) {
                            $builder->where('branch_id', $branchId);
                        }
                    });
                }
            }
        }
    }
}
