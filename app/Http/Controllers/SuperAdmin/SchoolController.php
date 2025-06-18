<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\SchoolCreate;
use App\Http\Requests\School\SchoolUpdate;
use App\Repositories\SchoolRepo;

class SchoolController extends Controller
{
    protected $school;

    public function __construct(SchoolRepo $school)
    {
        $this->middleware('super_admin');
        $this->school = $school;
    }

    public function index()
    {
        $d['schools'] = $this->school->getAll();
        return view('pages.super_admin.schools.index', $d);
    }

    public function store(SchoolCreate $req)
    {
        $data = $req->except('logo');
        $data['lock_exam'] = $req->lock_exam ? 1 : 0;
        $data['is_active'] = $req->is_active ? 1 : 0;

        if($req->hasFile('logo')) {
            $logo = $req->file('logo');
            $f = Qs::getFileMetaData($logo);
            $f['name'] = 'logo_' . time() . '.' . $f['ext'];
            $f['path'] = $logo->storeAs(Qs::getPublicUploadPath(), $f['name']);
            $data['logo'] = asset('storage/' . $f['path']);
        }

        $this->school->create($data);
        return Qs::jsonStoreOk();
    }

    public function edit($id)
    {
        $d['school'] = $school = $this->school->find($id);
        return is_null($school) ? Qs::goWithDanger('schools.index') : view('pages.super_admin.schools.edit', $d);
    }

    public function update(SchoolUpdate $req, $id)
    {
        $data = $req->except('logo');
        $data['lock_exam'] = $req->lock_exam ? 1 : 0;
        $data['is_active'] = $req->is_active ? 1 : 0;

        if($req->hasFile('logo')) {
            $logo = $req->file('logo');
            $f = Qs::getFileMetaData($logo);
            $f['name'] = 'logo_' . time() . '.' . $f['ext'];
            $f['path'] = $logo->storeAs(Qs::getPublicUploadPath(), $f['name']);
            $data['logo'] = asset('storage/' . $f['path']);
        }

        $this->school->update($id, $data);
        return Qs::jsonUpdateOk();
    }

    public function destroy($id)
    {
        $this->school->delete($id);
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
