<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dorm\DormCreate;
use App\Http\Requests\Dorm\DormUpdate;
use App\Repositories\DormRepo;

class DormController extends Controller
{
    protected  $dorm;

    public function __construct(DormRepo $dorm)
    {
        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->dorm = $dorm;
    }

    public function index()
    {
        // If user is not super admin, filter dorms by their school
        if (!Qs::userIsSuperAdmin() && auth()->user()->school_id) {
            $d['dorms'] = $this->dorm->getBySchool(auth()->user()->school_id);
        } else {
            $d['dorms'] = $this->dorm->getAll();
        }

        $d['schools'] = \App\Repositories\SchoolRepo::getAll();
        $d['user_school'] = auth()->user()->school_id;
        return view('pages.support_team.dorms.index', $d);
    }

    public function store(DormCreate $req)
    {
        $data = $req->only(['name', 'description', 'school_id']);

        // If user is not super admin, use their school_id
        if (!Qs::userIsSuperAdmin() && auth()->user()->school_id) {
            $data['school_id'] = auth()->user()->school_id;
        }

        $this->dorm->create($data);

        return Qs::jsonStoreOk();
    }

    public function edit($id)
    {
        $d['dorm'] = $dorm = $this->dorm->find($id);

        return !is_null($dorm) ? view('pages.support_team.dorms.edit', $d)
            : Qs::goWithDanger('dorms.index');
    }

    public function update(DormUpdate $req, $id)
    {
        $data = $req->only(['name', 'description']);
        $this->dorm->update($id, $data);

        return Qs::jsonUpdateOk();
    }

    public function destroy($id)
    {
        $this->dorm->find($id)->delete();
        return back()->with('flash_success', __('msg.delete_ok'));
    }
}
