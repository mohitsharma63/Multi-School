<?php

namespace App\Http\Controllers;

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

    public function get_lga(Request $req)
    {
        $s = $req->state_id;
        if(!$s) {
            return response()->json(['lgas' => []]);
        }
        $lgas = $this->loc->getLGAs($s);
        return response()->json(['lgas' => $lgas]);
    }

    public function getClassesBySchool(Request $req)
    {
        $schoolId = $req->school_id;
        if (!$schoolId) {
            return response()->json(['classes' => []]);
        }

        $classes = MyClass::where('school_id', $schoolId)->orderBy('name')->get();
        return response()->json(['classes' => $classes]);
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
