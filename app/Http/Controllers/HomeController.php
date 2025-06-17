<?php

namespace App\Http\Controllers;

use App\Helpers\Qs;
use App\Repositories\UserRepo;

class HomeController extends Controller
{
    protected $user;
    public function __construct(UserRepo $user)
    {
        $this->user = $user;
    }


    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function privacy_policy()
    {
        $data['app_name'] = config('app.name');
        $data['app_url'] = config('app.url');
        $data['contact_phone'] = Qs::getSetting('phone');
        return view('pages.other.privacy_policy', $data);
    }

    public function terms_of_use()
    {
        $data['app_name'] = config('app.name');
        $data['app_url'] = config('app.url');
        $data['contact_phone'] = Qs::getSetting('phone');
        return view('pages.other.terms_of_use', $data);
    }

    public function dashboard()
    {
        $d = [];
        $user = auth()->user();
        
        // Get current branch context
        $currentBranch = null;
        if ($user->isSuperAdmin()) {
            // Super admin can select any branch
            $currentBranch = session('selected_branch_id') ?? $user->branch_id;
            $d['branches'] = \App\Models\Branch::with('school')->active()->get();
        } elseif ($user->isSchoolAdmin()) {
            // School admin sees their school's branches
            $currentBranch = session('selected_branch_id') ?? $user->branch_id;
            $d['branches'] = \App\Models\Branch::where('school_id', $user->school_id)->active()->get();
        } else {
            // Branch-level users see only their branch
            $currentBranch = $user->branch_id;
        }
        
        $d['current_branch'] = $currentBranch;
        $d['selected_branch'] = $currentBranch ? \App\Models\Branch::find($currentBranch) : null;
        
        if(Qs::userIsTeamSAT() && $currentBranch){
            // Filter data by current branch
            $d['students_count'] = \App\Models\StudentRecord::where('branch_id', $currentBranch)->count();
            $d['staff_count'] = \App\User::where('branch_id', $currentBranch)
                ->whereIn('user_type', ['teacher', 'librarian', 'accountant'])
                ->count();
            $d['classes_count'] = \App\Models\MyClass::where('branch_id', $currentBranch)->count();
            $d['subjects_count'] = \App\Models\Subject::where('branch_id', $currentBranch)->count();
            
            // Recent activities for the branch
            $d['recent_students'] = \App\Models\StudentRecord::with('user')
                ->where('branch_id', $currentBranch)
                ->latest()
                ->limit(5)
                ->get();
                
            $d['pending_payments'] = \App\Models\PaymentRecord::whereHas('student', function($query) use ($currentBranch) {
                $query->where('branch_id', $currentBranch);
            })->where('paid', 0)->count();
        }

        return view('pages.support_team.dashboard', $d);
    }
    
    public function switchBranch(Request $request)
    {
        $user = auth()->user();
        $branchId = $request->input('branch_id');
        
        // Validate branch access
        if ($user->isSuperAdmin()) {
            // Super admin can access any branch
            session(['selected_branch_id' => $branchId]);
        } elseif ($user->isSchoolAdmin()) {
            // School admin can only access branches in their school
            $branch = \App\Models\Branch::where('id', $branchId)
                ->where('school_id', $user->school_id)
                ->first();
            if ($branch) {
                session(['selected_branch_id' => $branchId]);
            }
        }
        
        return redirect()->route('dashboard');
    }
}
