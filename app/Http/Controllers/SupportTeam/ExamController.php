<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Requests\Exam\ExamCreate;
use App\Http\Requests\Exam\ExamUpdate;
use App\Repositories\ExamRepo;
use App\Repositories\SchoolRepo;
use App\Http\Controllers\Controller;

class ExamController extends Controller
{
    protected $exam, $school;
    public function __construct(ExamRepo $exam, SchoolRepo $school)
    {
        $this->middleware('teamSA', ['except' => ['destroy',] ]);
        $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->exam = $exam;
        $this->school = $school;
    }

    public function index()
    {
        $d['exams'] = $this->exam->all();
        $d['schools'] = $this->school->getAll();
        return view('pages.support_team.exams.index', $d);
    }

    public function store(ExamCreate $req)
    {
        $data = $req->only(['name', 'term', 'school_id']);
        $data['year'] = Qs::getCurrentSession();

        $this->exam->create($data);

        return Qs::jsonStoreOk();
    }

    public function edit($id)
    {
        $d['ex'] = $this->exam->find($id);
        $d['schools'] = $this->school->getAll();
        return view('pages.support_team.exams.edit', $d);
    }

    public function update(ExamUpdate $req, $id)
    {
        $data = $req->only(['name', 'term', 'school_id']);

        $this->exam->update($id, $data);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function destroy($id)
    {
        $this->exam->delete($id);
        return back()->with('flash_success', __('msg.del_ok'));
    }
}
