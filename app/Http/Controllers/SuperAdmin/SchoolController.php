<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use App\Http\Requests\School\SchoolCreate;
use App\Http\Requests\School\SchoolUpdate;

class SchoolController extends Controller
{
    public function index()
    {
        $d['schools'] = School::orderBy('name')->get();
        return view('pages.super_admin.schools.index', $d);
    }

    public function store(SchoolCreate $req)
    {
        $data = $req->validated();

        if($req->hasFile('logo')) {
            $data['logo'] = $req->file('logo')->store('logos', 'public');
        }

        School::create($data);
        return back()->with('flash_success', 'School Created Successfully');
    }

    public function edit($id)
    {
        $d['school'] = School::findOrFail($id);
        return view('pages.super_admin.schools.edit', $d);
    }

    public function update(SchoolUpdate $req, $id)
    {
        $school = School::findOrFail($id);
        $data = $req->validated();

        if($req->hasFile('logo')) {
            $data['logo'] = $req->file('logo')->store('logos', 'public');
        }

        $school->update($data);
        return back()->with('flash_success', 'School Updated Successfully');
    }

    public function destroy($id)
    {
        School::destroy($id);
        return back()->with('flash_success', 'School Deleted Successfully');
    }
}
