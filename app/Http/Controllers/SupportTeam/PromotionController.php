<?php

namespace App\Http\Controllers\SupportTeam;

use App\Helpers\Qs;
use App\Http\Controllers\Controller;
use App\Models\Mark;
use App\Repositories\MyClassRepo;
use App\Repositories\StudentRepo;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    protected $my_class, $student;

    public function __construct(MyClassRepo $my_class, StudentRepo $student)
    {
        $this->middleware('teamSA');

        $this->my_class = $my_class;
        $this->student = $student;
    }

    public function promotion($fc = NULL, $fs = NULL, $tc = NULL, $ts = NULL)
    {
        $d['old_year'] = $old_yr = Qs::getSetting('current_session');
        $old_yr = explode('-', $old_yr);

        // Validate the session format and provide fallback
        if (count($old_yr) < 2 || !is_numeric($old_yr[0]) || !is_numeric($old_yr[1])) {
            // Fallback to current year if session format is invalid
            $current_year = date('Y');
            $d['new_year'] = $current_year . '-' . ($current_year + 1);
        } else {
            $d['new_year'] = ++$old_yr[0].'-'.++$old_yr[1];
        }

        // Get classes for current school or all schools for super admin
        $current_school_id = Qs::getSetting('current_school_id') ?? 1;
        if (Qs::userIsSuperAdmin()) {
            $d['my_classes'] = $this->my_class->with('school')->orderBy('school_id')->get();
            $d['schools'] = \App\Models\School::orderBy('name')->get();
        } else {
            $d['my_classes'] = $this->my_class->with('school')->where('school_id', $current_school_id)->orderBy('name')->get();
            $d['schools'] = collect(); // Empty collection for non-super admin
        }

        // Get sections based on school filtering with proper relationships
        if (Qs::userIsSuperAdmin()) {
            $d['sections'] = $this->my_class->getAllSections()->load('my_class.school');
        } else {
            $d['sections'] = $this->my_class->getAllSections()->filter(function($section) use ($current_school_id) {
                return $section->my_class && $section->my_class->school_id == $current_school_id;
            });
        }

        $d['selected'] = false;
        $d['current_school_id'] = $current_school_id;

        if($fc && $fs && $tc && $ts){
            // Validate that selected classes belong to the user's school (if not super admin)
            if (!Qs::userIsSuperAdmin()) {
                $from_class = $this->my_class->find($fc);
                $to_class = $this->my_class->find($tc);

                if (!$from_class || !$to_class ||
                    $from_class->school_id != $current_school_id ||
                    $to_class->school_id != $current_school_id) {
                    return redirect()->route('students.promotion')->with('flash_danger', 'Invalid class selection for your school.');
                }
            }

            // Validate that from and to classes belong to the same school
            $from_class = $this->my_class->find($fc);
            $to_class = $this->my_class->find($tc);

            if ($from_class && $to_class && $from_class->school_id != $to_class->school_id) {
                return redirect()->route('students.promotion')->with('flash_danger', 'Cannot promote students between different schools.');
            }

            $d['selected'] = true;
            $d['fc'] = $fc;
            $d['fs'] = $fs;
            $d['tc'] = $tc;
            $d['ts'] = $ts;

            // Get students with proper school filtering and relationships
            $query = $this->student->getRecord(['my_class_id' => $fc, 'section_id' => $fs, 'session' => $d['old_year']])
                ->with(['user', 'my_class.school', 'section']);

            // Add school filtering for non-super admin users
            if (!Qs::userIsSuperAdmin()) {
                $query->whereHas('my_class', function($q) use ($current_school_id) {
                    $q->where('school_id', $current_school_id);
                });
            }

            $d['students'] = $sts = $query->get();
            $d['from_class'] = $from_class;
            $d['to_class'] = $to_class;
            $d['from_section'] = $d['sections']->where('id', $fs)->first();
            $d['to_section'] = $d['sections']->where('id', $ts)->first();

            if($sts->count() < 1){
                return redirect()->route('students.promotion')->with('flash_info', 'No students found for promotion in the selected class and section.');
            }
        }

        return view('pages.support_team.students.promotion.index', $d);
    }

    public function selector(Request $req)
    {
        return redirect()->route('students.promotion', [$req->fc, $req->fs, $req->tc, $req->ts]);
    }

    public function promote(Request $req, $fc, $fs, $tc, $ts)
    {
        $current_school_id = Qs::getSetting('current_school_id') ?? 1;

        // Validate that selected classes belong to the user's school (if not super admin)
        if (!Qs::userIsSuperAdmin()) {
            $from_class = $this->my_class->find($fc);
            $to_class = $this->my_class->find($tc);

            if (!$from_class || !$to_class ||
                $from_class->school_id != $current_school_id ||
                $to_class->school_id != $current_school_id) {
                return redirect()->route('students.promotion')->with('flash_danger', 'Invalid class selection for your school.');
            }
        }

        $oy = Qs::getSetting('current_session');
        $old_yr = explode('-', $oy);

        // Validate the session format and provide fallback
        if (count($old_yr) < 2 || !is_numeric($old_yr[0]) || !is_numeric($old_yr[1])) {
            // Fallback to current year if session format is invalid
            $current_year = date('Y');
            $ny = $current_year . '-' . ($current_year + 1);
        } else {
            $ny = ++$old_yr[0].'-'.++$old_yr[1];
        }

        // Get students with proper school filtering
        $query = $this->student->getRecord(['my_class_id' => $fc, 'section_id' => $fs, 'session' => $oy]);

        // Add school filtering for non-super admin users
        if (!Qs::userIsSuperAdmin()) {
            $query->whereHas('my_class', function($q) use ($current_school_id) {
                $q->where('school_id', $current_school_id);
            });
        }

        $students = $query->get()->sortBy('user.name');

        if($students->count() < 1){
            return redirect()->route('students.promotion')->with('flash_danger', __('msg.srnf'));
        }

        // Track promotion statistics
        $stats = ['promoted' => 0, 'repeated' => 0, 'graduated' => 0];
        $errors = [];

        foreach($students as $st){
            $p = 'p-'.$st->id;
            $p = $req->$p;

            // Skip if no action specified
            if (!$p) continue;

            try {
                // Reset $d array for each student
                $d = [];

                if($p === 'P'){ // Promote
                    $d['my_class_id'] = $tc;
                    $d['section_id'] = $ts;
                    $d['session'] = $ny;
                    $d['grad'] = 0;
                    $d['grad_date'] = null;
                    $stats['promoted']++;
                }
                if($p === 'D'){ // Don't Promote
                    $d['my_class_id'] = $fc;
                    $d['section_id'] = $fs;
                    $d['session'] = $ny;
                    $d['grad'] = 0;
                    $d['grad_date'] = null;
                    $stats['repeated']++;
                }
                if($p === 'G'){ // Graduated
                    $d['my_class_id'] = $fc;
                    $d['section_id'] = $fs;
                    $d['session'] = $ny;
                    $d['grad'] = 1;
                    $d['grad_date'] = $oy;
                    $stats['graduated']++;
                }

                $this->student->updateRecord($st->id, $d);

                // Insert New Promotion Data
                $promote = [
                    'from_class' => $fc,
                    'from_section' => $fs,
                    'grad' => ($p === 'G') ? 1 : 0,
                    'to_class' => in_array($p, ['D', 'G']) ? $fc : $tc,
                    'to_section' => in_array($p, ['D', 'G']) ? $fs : $ts,
                    'student_id' => $st->user_id,
                    'from_session' => $oy,
                    'to_session' => $ny,
                    'status' => $p
                ];

                $this->student->createPromotion($promote);

            } catch (\Exception $e) {
                $errors[] = "Error processing {$st->user->name}: " . $e->getMessage();
            }
        }

        // Create success message with statistics
        $message = "Promotion completed successfully! ";
        $message .= "Promoted: {$stats['promoted']}, ";
        $message .= "Repeated: {$stats['repeated']}, ";
        $message .= "Graduated: {$stats['graduated']}";

        if (!empty($errors)) {
            $message .= ". However, there were some errors: " . implode('; ', $errors);
        }

        return redirect()->route('students.promotion')->with('flash_success', $message);
    }

    public function manage()
    {
        $current_school_id = Qs::getSetting('current_school_id') ?? 1;

        // Get promotions with proper school filtering and relationships
        if (Qs::userIsSuperAdmin()) {
            $data['promotions'] = $this->student->getAllPromotions()
                ->load(['student', 'fc.school', 'tc.school', 'fs', 'ts'])
                ->sortBy('fc.school.name')
                ->sortBy('student.name');
            $data['schools'] = \App\Models\School::orderBy('name')->get();
        } else {
            // Filter promotions by school for non-super admin users
            $data['promotions'] = $this->student->getAllPromotions()
                ->load(['student', 'fc.school', 'tc.school', 'fs', 'ts'])
                ->filter(function($promotion) use ($current_school_id) {
                    return $promotion->fc && $promotion->fc->school_id == $current_school_id;
                })
                ->sortBy('student.name');
            $data['schools'] = collect(); // Empty collection
        }

        $data['old_year'] = Qs::getCurrentSession();
        $data['new_year'] = Qs::getNextSession();
        $data['current_school_id'] = $current_school_id;

        // Calculate statistics
        $promotions = $data['promotions'];
        $data['statistics'] = [
            'total' => $promotions->count(),
            'promoted' => $promotions->where('status', 'P')->count(),
            'repeated' => $promotions->where('status', 'D')->count(),
            'graduated' => $promotions->where('status', 'G')->count(),
        ];

        return view('pages.support_team.students.promotion.reset', $data);
    }

    public function reset($promotion_id)
    {
        $this->reset_single($promotion_id);

        return redirect()->route('students.promotion_manage')->with('flash_success', __('msg.update_ok'));
    }

    public function reset_all()
    {
        $next_session = Qs::getNextSession();
        $where = ['from_session' => Qs::getCurrentSession(), 'to_session' => $next_session];
        $proms = $this->student->getPromotions($where);

        if ($proms->count()){
          foreach ($proms as $prom){
              $this->reset_single($prom->id);

              // Delete Marks if Already Inserted for New Session
              $this->delete_old_marks($prom->student_id, $next_session);
          }
        }

        return Qs::jsonUpdateOk();
    }

    protected function delete_old_marks($student_id, $year)
    {
        Mark::where(['student_id' => $student_id, 'year' => $year])->delete();
    }

    protected function reset_single($promotion_id)
    {
        $prom = $this->student->findPromotion($promotion_id);

        $data['my_class_id'] = $prom->from_class;
        $data['section_id'] = $prom->from_section;
        $data['session'] = $prom->from_session;
        $data['grad'] = 0;
        $data['grad_date'] = null;

        $this->student->update(['user_id' => $prom->student_id], $data);

        return $this->student->deletePromotion($promotion_id);
    }
}
