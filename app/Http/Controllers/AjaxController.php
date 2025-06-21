<?php

namespace App\Http\Controllers;

use App\Models\Lga;
use App\Helpers\Qs;
use App\Models\Mark;
use App\Models\MyClass;
use App\Repositories\LocationRepo;
use App\Repositories\MyClassRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    protected $loc, $my_class;

    public function __construct(LocationRepo $loc, MyClassRepo $my_class)
    {
        $this->loc = $loc;
        $this->my_class = $my_class;
    }

    public function get_lga(Request $req, $state_id = null)
    {
        $s = $state_id ?: $req->state_id;
        if(!$s) {
            return response()->json([]);
        }
        $lgas = $this->loc->getLGAs($s);
        return response()->json($lgas);
    }

    public function getClassesBySchool(Request $req, $school_id = null)
    {
        try {
            $id = $school_id ?: $req->school_id;

            if (!$id) {
                return response()->json(['classes' => []]);
            }

            $classes = MyClass::where('school_id', $id)
                            ->orderBy('name')
                            ->get(['id', 'name', 'school_id']);

            return response()->json([
                'success' => true,
                'classes' => $classes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching classes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get_class_sections(Request $req)
    {
        $class_id = $req->class_id;
        if(!$class_id) {
            return response()->json([]);
        }

        $sections = $this->my_class->getClassSections($class_id);
        return $sections = $sections->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
    }

    public function get_class_subjects(Request $req)
    {
        $class_id = $req->class_id;
        if(!$class_id) {
            return response()->json(['sections' => [], 'subjects' => []]);
        }

        $sections = $this->my_class->getClassSections($class_id);
        $subjects = $this->my_class->findSubjectByClass($class_id);

        if(Qs::userIsTeacher()){
            $subjects = $this->my_class->findSubjectByTeacher(Auth::user()->id)->where('my_class_id', $class_id);
        }

        $d['sections'] = $sections->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();
        $d['subjects'] = $subjects->map(function($q){
            return ['id' => $q->id, 'name' => $q->name];
        })->all();

        return $d;
    }
}
