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
        $d=[];
        $branch_id = request('branch_id');

        // Get user's default branch if no branch_id is provided
        $user_default_branch = auth()->user()->branch_id ?? null;
        $selected_branch_id = $branch_id ?? $user_default_branch;

        if(Qs::userIsTeamSAT()){
            if($selected_branch_id) {
                $d['users'] = $this->user->getByBranch($selected_branch_id);
            } else {
                $d['users'] = $this->user->getAll();
            }
        }

        // Get all branches for dropdown
        $d['branches'] = \App\Models\Branch::where('is_active', true)->get();

        // Set the selected branch (either from URL parameter or user's default branch)
        $d['user_branch'] = $selected_branch_id;
        $d['selected_branch'] = $selected_branch_id;

        return view('pages.support_team.dashboard', $d);
    }
}
