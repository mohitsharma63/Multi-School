<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\School;
use Illuminate\Http\Request;
use App\Http\Requests\Branch\BranchCreate;
use App\Http\Requests\Branch\BranchUpdate;

class BranchController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if($user->isSuperAdmin()) {
            $d['branches'] = Branch::with('school')->orderBy('name')->get();
        } elseif($user->isSchoolAdmin()) {
            $d['branches'] = Branch::with('school')->where('school_id', $user->school_id)->get();
        } else {
            $d['branches'] = Branch::with('school')->where('id', $user->branch_id)->get();
        }

        $d['schools'] = School::active()->orderBy('name')->get();
        return view('pages.super_admin.branches.index', $d);
    }

    public function store(BranchCreate $req)
    {
        Branch::create($req->validated());
        return back()->with('flash_success', 'Branch Created Successfully');
    }

    public function edit($id)
    {
        $d['branch'] = Branch::findOrFail($id);
        $d['schools'] = School::active()->orderBy('name')->get();
        return view('pages.super_admin.branches.edit', $d);
    }

    public function update(BranchUpdate $req, $id)
    {
        $branch = Branch::findOrFail($id);
        $branch->update($req->validated());
        return back()->with('flash_success', 'Branch Updated Successfully');
    }

    public function destroy($id)
    {
        Branch::destroy($id);
        return back()->with('flash_success', 'Branch Deleted Successfully');
    }
}
