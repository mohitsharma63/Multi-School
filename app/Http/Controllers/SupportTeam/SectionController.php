<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Requests\Section\SectionCreate;
use App\Http\Requests\Section\SectionUpdate;
use App\Repositories\MyClassRepo;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepo;
use App\Repositories\SchoolRepo;

class SectionController extends Controller
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
        // If user is admin, only show classes and sections from their assigned school
        if (auth()->user()->user_type == 'admin' && auth()->user()->school_id) {
            $d['my_classes'] = $this->my_class->getBySchool(auth()->user()->school_id);
            $d['sections'] = $this->my_class->getAllSections()->filter(function($section) {
                return $section->my_class && $section->my_class->school_id == auth()->user()->school_id;
            });
            $d['schools'] = $this->school->find(auth()->user()->school_id) ? [$this->school->find(auth()->user()->school_id)] : [];
        } else {
            $d['my_classes'] = $this->my_class->all();
            $d['sections'] = $this->my_class->getAllSections();
            $d['schools'] = SchoolRepo::getAll();
        }

        $d['teachers'] = $this->user->getUserByType('teacher');
        $d['user_school'] = auth()->user()->school_id;

        return view('pages.support_team.sections.index', $d);
    }

    public function store(SectionCreate $req)
    {
        $data = $req->all();
        $this->my_class->createSection($data);

        return Qs::jsonStoreOk();
    }

    public function edit($id)
    {
        $d['s'] = $s = $this->my_class->findSection($id);
        $d['teachers'] = $this->user->getUserByType('teacher');

        return is_null($s) ? Qs::goWithDanger('sections.index') :view('pages.support_team.sections.edit', $d);
    }

    public function update(SectionUpdate $req, $id)
    {
        $data = $req->only(['name', 'teacher_id']);
        $this->my_class->updateSection($id, $data);

        return Qs::jsonUpdateOk();
    }

    public function destroy($id)
    {
        if($this->my_class->isActiveSection($id)){
            return back()->with('pop_warning', 'Every class must have a default section, You Cannot Delete It');
        }

        $this->my_class->deleteSection($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }

    public function getClassesBySchool($schoolId)
    {
        $classes = $this->my_class->getWhere(['school_id' => $schoolId]);
        return response()->json(['classes' => $classes]);
    }

}
