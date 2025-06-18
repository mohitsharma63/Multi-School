<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Http\Requests\School\SchoolCreate;
use App\Http\Requests\School\SchoolUpdate;
use App\Helpers\Qs;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::with('branches')->orderBy('name')->get();
        return view('pages.super_admin.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('pages.super_admin.schools.create');
    }

    public function store(SchoolCreate $request)
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);

        if($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $f = Qs::getFileMetaData($logo);
            $f['name'] = 'school_logo_' . time() . '.' . $f['ext'];
            $f['path'] = $logo->storeAs(Qs::getPublicUploadPath(), $f['name']);
            $data['logo'] = asset('storage/' . $f['path']);
        }

        School::create($data);

        return redirect()->route('schools.index')->with('flash_success', __('msg.store_ok'));
    }

    public function show(School $school)
    {
        $school->load('branches');
        return view('pages.super_admin.schools.show', compact('school'));
    }

    public function edit(School $school)
    {
        return view('pages.super_admin.schools.edit', compact('school'));
    }

    public function update(SchoolUpdate $request, School $school)
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);

        if($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $f = Qs::getFileMetaData($logo);
            $f['name'] = 'school_logo_' . time() . '.' . $f['ext'];
            $f['path'] = $logo->storeAs(Qs::getPublicUploadPath(), $f['name']);
            $data['logo'] = asset('storage/' . $f['path']);
        }

        $school->update($data);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function destroy(School $school)
    {
        $school->delete();
        return back()->with('flash_success', __('msg.del_ok'));
    }
}
