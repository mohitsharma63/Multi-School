<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Helpers\Mk;
use App\Http\Requests\Student\StudentRecordCreate;
use App\Http\Requests\Student\StudentRecordUpdate;
use App\Repositories\LocationRepo;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use App\Repositories\UserRepo;
use App\Repositories\DormRepo;
use App\Repositories\SchoolRepo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class StudentRecordController extends Controller
{
    protected $user, $my_class, $student, $loc, $dorm, $school;

   public function __construct(UserRepo $user, MyClassRepo $my_class, StudentRepo $student, LocationRepo $loc, DormRepo $dorm, SchoolRepo $school)
   {
       $this->middleware('teamSA', ['only' => ['edit','update', 'reset_pass', 'create', 'store', 'graduated'] ]);
       $this->middleware('super_admin', ['only' => ['destroy',] ]);

        $this->user = $user;
        $this->my_class = $my_class;
        $this->student = $student;
        $this->loc = $loc;
        $this->dorm = $dorm;
        $this->school = $school;
    }

    public function reset_pass($st_id)
    {
        $st_id = Qs::decodeHash($st_id);
        $data['password'] = Hash::make('student');
        $this->user->update($st_id, $data);
        return back()->with('flash_success', __('msg.p_reset'));
    }

    public function create()
    {
        // Initialize all data with proper null checking
        $data['my_classes'] = $this->my_class->all() ?? collect();
        $data['parents'] = $this->user->getUserByType('parent') ?? collect();
        $data['dorms'] = $this->student->getAllDorms() ?? collect();
        $data['states'] = $this->loc->getStates() ?? collect();
        $data['nationals'] = $this->loc->getAllNationals() ?? collect();
        $data['schools'] = $this->school->getAll() ?? collect();

        // Add schools data for super admin users
        if (Qs::userIsSuperAdmin()) {
            $data['schools'] = \App\Models\School::all() ?? collect();
        } else {
            $data['schools'] = collect();
        }

        // Debug: Check if states data exists
        if(!$data['states'] || $data['states']->count() == 0) {
            \Log::warning('No states found in database');
        }

        return view('pages.support_team.students.add', $data);
    }

    /**
     * Get sections for a specific class (AJAX endpoint)
     */
    public function getClassSections(Request $request)
    {
        $class_id = $request->get('class_id');

        if (!$class_id) {
            return response()->json(['error' => 'Class ID is required'], 400);
        }

        try {
            $sections = $this->my_class->getClassSections($class_id);

            // Format sections for select dropdown
            $formatted_sections = $sections->map(function($section) {
                return [
                    'id' => $section->id,
                    'name' => $section->name ?? 'Unknown Section'
                ];
            });

            return response()->json([
                'success' => true,
                'sections' => $formatted_sections
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching class sections: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch sections'], 500);
        }
    }

    public function store(StudentRecordCreate $req)
    {
       $data =  $req->only(Qs::getUserRecord());
       $sr =  $req->only(Qs::getStudentData());

        // Add school ID to student record
        $current_school_id = Qs::getSetting('current_school_id');
        $sr['school_id'] = $req->school_id ?? $current_school_id ?? 1;

        $ct = $this->my_class->findTypeByClass($req->my_class_id)->code;
       /* $ct = ($ct == 'J') ? 'JSS' : $ct;
        $ct = ($ct == 'S') ? 'SS' : $ct;*/

        $data['user_type'] = 'student';
        $data['name'] = ucwords($req->name);
        $data['code'] = strtoupper(Str::random(10));
        $data['password'] = Hash::make('student');
        $data['photo'] = Qs::getDefaultUserImage();
        $adm_no = $req->adm_no;
        $data['username'] = strtoupper(Qs::getAppCode().'/'.$ct.'/'.$sr['year_admitted'].'/'.($adm_no ?: mt_rand(1000, 99999)));

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = Qs::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(Qs::getUploadPath('student').$data['code'], $f['name']);
            $data['photo'] = asset('storage/' . $f['path']);
        }

        $user = $this->user->create($data); // Create User

        $sr['adm_no'] = $data['username'];
        $sr['user_id'] = $user->id;
        $sr['session'] = Qs::getSetting('current_session') ?? date('Y') . '-' . (date('Y') + 1);

        $this->student->createRecord($sr); // Create Student
        return Qs::jsonStoreOk();
    }

    public function listByClass($class_id)
    {
        $data['my_class'] = $mc = $this->my_class->getMC(['id' => $class_id])->first();
        $data['students'] = $this->student->findStudentsByClass($class_id);
        $data['sections'] = $this->my_class->getClassSections($class_id);

        return is_null($mc) ? Qs::goWithDanger() : view('pages.support_team.students.list', $data);
    }

    public function graduated()
    {
        $current_school_id = Qs::getSetting('current_school_id') ?? 1;

        // Get graduated students with proper school filtering
        if (Qs::userIsSuperAdmin()) {
            $data['students'] = $this->student->allGradStudents()->load(['user', 'my_class.school', 'section']);
            $data['my_classes'] = $this->my_class->with('school')->orderBy('school_id')->get();
            $data['schools'] = \App\Models\School::orderBy('name')->get();
        } else {
            // Filter by current school for non-super admin
            $data['students'] = $this->student->allGradStudents()
                ->load(['user', 'my_class.school', 'section'])
                ->filter(function($student) use ($current_school_id) {
                    return $student->my_class && $student->my_class->school_id == $current_school_id;
                });
            $data['my_classes'] = $this->my_class->with('school')->where('school_id', $current_school_id)->get();
            $data['schools'] = collect();
        }

        $data['current_school_id'] = $current_school_id;

        // Add schools data for filtering
        if (Qs::userIsSuperAdmin()) {
            $data['schools'] = \App\Models\School::all();
        }

        return view('pages.support_team.students.graduated', $data);
    }

    public function not_graduated($sr_id)
    {
        $d['grad'] = 0;
        $d['grad_date'] = NULL;
        $d['session'] = Qs::getSetting('current_session');
        $this->student->updateRecord($sr_id, $d);

        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function show($sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        if(!$sr_id){return Qs::goWithDanger();}

        $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();

        /* Prevent Other Students/Parents from viewing Profile of others */
        if(Auth::user()->id != $data['sr']->user_id && !Qs::userIsTeamSAT() && !Qs::userIsMyChild($data['sr']->user_id, Auth::user()->id)){
            return redirect(route('dashboard'))->with('pop_error', __('msg.denied'));
        }

        return view('pages.support_team.students.show', $data);
    }

    public function edit($sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        if(!$sr_id){return Qs::goWithDanger();}

        $data['sr'] = $this->student->getRecord(['id' => $sr_id])->first();
        $data['my_classes'] = $this->my_class->all();
        $data['parents'] = $this->user->getUserByType('parent');
        $data['dorms'] = $this->student->getAllDorms();
        $data['states'] = $this->loc->getStates();
        $data['nationals'] = $this->loc->getAllNationals();

        // Get sections for the current class
        if($data['sr']->my_class_id) {
            $data['sections'] = $this->my_class->getClassSections($data['sr']->my_class_id);
        } else {
            $data['sections'] = collect();
        }

        return view('pages.support_team.students.edit', $data);
    }

    public function update(StudentRecordUpdate $req, $sr_id)
    {
        $sr_id = Qs::decodeHash($sr_id);
        if(!$sr_id){return Qs::goWithDanger();}

        $sr = $this->student->getRecord(['id' => $sr_id])->first();
        $d =  $req->only(Qs::getUserRecord());
        $d['name'] = ucwords($req->name);

        if($req->hasFile('photo')) {
            $photo = $req->file('photo');
            $f = Qs::getFileMetaData($photo);
            $f['name'] = 'photo.' . $f['ext'];
            $f['path'] = $photo->storeAs(Qs::getUploadPath('student').$sr->user->code, $f['name']);
            $d['photo'] = asset('storage/' . $f['path']);
        }

        $this->user->update($sr->user->id, $d); // Update User Details

        $srec = $req->only(Qs::getStudentData());

        $this->student->updateRecord($sr_id, $srec); // Update St Rec

        /*** If Class/Section is Changed in Same Year, Delete Marks/ExamRecord of Previous Class/Section ****/
        Mk::deleteOldRecord($sr->user->id, $srec['my_class_id']);

        return Qs::jsonUpdateOk();
    }

    public function destroy($st_id)
    {
        $st_id = Qs::decodeHash($st_id);
        if(!$st_id){return Qs::goWithDanger();}

        $sr = $this->student->getRecord(['user_id' => $st_id])->first();
        $path = Qs::getUploadPath('student').$sr->user->code;
        Storage::exists($path) ? Storage::deleteDirectory($path) : false;
        $this->user->delete($sr->user->id);

        return back()->with('flash_success', __('msg.del_ok'));
    }
}
