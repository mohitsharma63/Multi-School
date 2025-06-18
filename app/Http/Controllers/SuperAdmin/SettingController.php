<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Requests\SettingUpdate;
use App\Repositories\MyClassRepo;
use App\Repositories\SettingRepo;

class SettingController extends Controller
{
    protected $setting, $my_class;

    public function __construct(SettingRepo $setting, MyClassRepo $my_class)
    {
        $this->setting = $setting;
        $this->my_class = $my_class;
    }

    public function index()
    {
        $school_id = request('school_id');
        $branch_id = request('branch_id');

        // Get user's default branch if no branch_id is provided
        $user_default_branch = auth()->user()->branch_id ?? null;
        $selected_branch_id = $branch_id ?? $user_default_branch;

        $s = $this->setting->all();
        $d['class_types'] = $this->my_class->getTypes();
        $d['s'] = $s->flatMap(function($s){
            return [$s->type => $s->description];
        });

        // Get all schools for dropdown
        $d['schools'] = \App\Models\School::where('is_active', true)->with('branches')->get();
        $d['selected_school'] = $school_id;
        $d['selected_branch'] = $selected_branch_id;
        $d['user_branch'] = $selected_branch_id;

        // Get branches for selected school
        if($school_id) {
            $d['branches'] = \App\Models\Branch::where('school_id', $school_id)->where('is_active', true)->get();
        } else {
            $d['branches'] = collect();
        }

        // If school is selected, override settings with school data
        if($school_id) {
            $school = \App\Models\School::find($school_id);
            if($school) {
                $d['s']['system_name'] = $school->name;
                $d['s']['system_title'] = $school->system_title;
                $d['s']['address'] = $school->address;
                $d['s']['phone'] = $school->phone ?? $d['s']['phone'];
                $d['s']['system_email'] = $school->email ?? $d['s']['system_email'];
                $d['s']['current_session'] = $school->current_session ?? $d['s']['current_session'];
                $d['s']['term_begins'] = $school->term_begins ?? $d['s']['term_begins'];
                $d['s']['term_ends'] = $school->term_ends ?? $d['s']['term_ends'];
                $d['s']['logo'] = $school->logo ?? $d['s']['logo'];
            }
        }

        // If branch is selected, override with branch data
        if($selected_branch_id) {
            $branch = \App\Models\Branch::with('school')->find($selected_branch_id);
            if($branch) {
                $d['s']['system_name'] = $branch->school->name . ' - ' . $branch->name;
                $d['s']['address'] = $branch->address;
                $d['s']['phone'] = $branch->phone ?? $d['s']['phone'];
                $d['s']['system_email'] = $branch->email ?? $d['s']['system_email'];
            }
        }

        return view('pages.super_admin.settings', $d);
    }

    public function update(SettingUpdate $req)
    {
        $school_id = $req->input('school_id');
        $branch_id = $req->input('branch_id');
        $sets = $req->except('_token', '_method', 'logo', 'school_id', 'branch_id');
        $sets['lock_exam'] = $sets['lock_exam'] == 1 ? 1 : 0;

        // If school is selected, update school-specific information
        if($school_id) {
            $school = \App\Models\School::find($school_id);
            if($school) {
                $schoolData = [
                    'name' => $sets['system_name'],
                    'system_title' => $sets['system_title'] ?? $school->system_title,
                    'address' => $sets['address'],
                    'phone' => $sets['phone'] ?? $school->phone,
                    'email' => $sets['system_email'] ?? $school->email,
                    'current_session' => $sets['current_session'] ?? $school->current_session,
                    'term_begins' => $sets['term_begins'] ?? $school->term_begins,
                    'term_ends' => $sets['term_ends'] ?? $school->term_ends,
                ];

                if($req->hasFile('logo')) {
                    $logo = $req->file('logo');
                    $f = Qs::getFileMetaData($logo);
                    $f['name'] = 'school_logo_' . $school_id . '.' . $f['ext'];
                    $f['path'] = $logo->storeAs(Qs::getPublicUploadPath(), $f['name']);
                    $schoolData['logo'] = asset('storage/' . $f['path']);
                }

                $school->update($schoolData);
                unset($sets['system_name'], $sets['system_title'], $sets['address'], $sets['phone'], $sets['system_email'], $sets['current_session'], $sets['term_begins'], $sets['term_ends']);
            }
        }

        // If branch is selected, update branch-specific information
        if($branch_id) {
            $branch = \App\Models\Branch::find($branch_id);
            if($branch) {
                $branch->update([
                    'address' => $sets['address'],
                    'phone' => $sets['phone'] ?? $branch->phone,
                    'email' => $sets['system_email'] ?? $branch->email,
                ]);
                unset($sets['address'], $sets['phone'], $sets['system_email']);
            }
        }

        // Update global settings
        $keys = array_keys($sets);
        $values = array_values($sets);
        for($i=0; $i<count($sets); $i++){
            $this->setting->update($keys[$i], $values[$i]);
        }

        return back()->with('flash_success', __('msg.update_ok'));
    }
}
