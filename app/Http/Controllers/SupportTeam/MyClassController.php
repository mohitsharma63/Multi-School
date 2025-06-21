<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Requests\MyClass\ClassCreate;
use App\Http\Requests\MyClass\ClassUpdate;
use App\Repositories\MyClassRepo;
use App\Repositories\UserRepo;
use App\Repositories\SchoolRepo;
use App\Http\Controllers\Controller;

class MyClassController extends Controller
{
    protected $my_class, $user, $school;

    public function __construct(MyClassRepo $my_class, UserRepo $user, SchoolRepo $school)
    {
        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->my_class = $my_class;
        $this->user = $user;
        $this->school = $school;
    }

    public function index()
    {
        // If user is admin, only show classes from their assigned school
        if (auth()->user()->user_type == 'admin' && auth()->user()->school_id) {
            $d['my_classes'] = $this->my_class->getBySchool(auth()->user()->school_id);
            $d['schools'] = $this->school->find(auth()->user()->school_id) ? [$this->school->find(auth()->user()->school_id)] : [];
        } else {
            $d['my_classes'] = $this->my_class->all();
            $d['schools'] = $this->school->getAll();
        }

        $d['class_types'] = $this->my_class->getTypes();

        return view('pages.support_team.classes.index', $d);
    }

    public function store(ClassCreate $req)
    {
        $data = $req->only(['name', 'class_type_id', 'school_id']);

        // Ensure school_id is provided
        if (empty($data['school_id'])) {
            return Qs::json('School selection is required', false);
        }

        // If user is admin, ensure they can only create classes for their assigned school
        if (auth()->user()->user_type == 'admin' && auth()->user()->school_id) {
            if ($data['school_id'] != auth()->user()->school_id) {
                return Qs::json('You can only create classes for your assigned school', false);
            }
        }

        $mc = $this->my_class->create($data);

        // Create Default Section
        $s = [
            'my_class_id' => $mc->id,
            'name' => 'A',
            'active' => 1,
            'teacher_id' => NULL,
        ];

        $this->my_class->createSection($s);

        return Qs::jsonStoreOk();
    }

    public function edit($id)
    {
        $d['c'] = $c = $this->my_class->find($id);

        return is_null($c) ? Qs::goWithDanger('classes.index') : view('pages.support_team.classes.edit', $d) ;
    }

    public function update(ClassUpdate $req, $id)
    {
        $data = $req->only(['name']);
        $this->my_class->update($id, $data);

        return Qs::jsonUpdateOk();
    }

    public function destroy($id)
    {
        $this->my_class->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }

}
