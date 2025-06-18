<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Http\Requests\Branch\BranchCreate;
use App\Http\Requests\Branch\BranchUpdate;
use App\Helpers\Qs;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with('school')->orderBy('name')->get();
        $schools = \App\Models\School::where('is_active', true)->get();
        return view('pages.super_admin.branches.index', compact('branches', 'schools'));
    }

    public function create()
    {
        $schools = \App\Models\School::where('is_active', true)->get();
        return view('pages.super_admin.branches.create', compact('schools'));
    }

    public function store(BranchCreate $request)
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);

        Branch::create($data);

        return redirect()->route('branches.index')->with('flash_success', __('msg.store_ok'));
    }

    public function show(Branch $branch)
    {
        return view('pages.super_admin.branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        $schools = \App\Models\School::where('is_active', true)->get();
        return view('pages.super_admin.branches.edit', compact('branch', 'schools'));
    }

    public function update(BranchUpdate $request, Branch $branch)
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);

        $branch->update($data);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return back()->with('flash_success', __('msg.del_ok'));
    }
}
