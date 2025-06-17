<?php

namespace App\Http\Controllers\Api;

use App\Models\StudentRecord;
use Illuminate\Http\Request;

class StudentController extends BaseApiController
{
    public function index(Request $request)
    {
        $query = StudentRecord::with(['user', 'my_class', 'section', 'branch']);

        // Apply branch filtering
        $query = $this->applyBranchFilter($query, $request);

        // Additional filters
        if ($request->has('class_id')) {
            $query->where('my_class_id', $request->class_id);
        }

        if ($request->has('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $students = $query->paginate($request->get('per_page', 15));

        return $this->successResponse($students);
    }

    public function show($id, Request $request)
    {
        $query = StudentRecord::with(['user', 'my_class', 'section', 'branch']);

        // Apply branch filtering
        $query = $this->applyBranchFilter($query, $request);

        $student = $query->findOrFail($id);

        return $this->successResponse($student);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users',
            'my_class_id' => 'required|exists:my_classes,id',
            'section_id' => 'required|exists:sections,id',
            'adm_no' => 'required|string|unique:student_records',
        ]);

        $user = auth('api')->user();

        $userData = $request->only(['name', 'email', 'gender', 'address', 'phone']);
        $userData['user_type'] = 'student';
        $userData['password'] = \Hash::make('student');
        $userData['code'] = strtoupper(\Str::random(10));

        // Set branch and school based on user permissions
        if (!$user->isSuperAdmin()) {
            $userData['branch_id'] = $user->branch_id;
            $userData['school_id'] = $user->school_id;
        } else {
            $userData['branch_id'] = $request->branch_id;
            $userData['school_id'] = $request->school_id;
        }

        $newUser = \App\User::create($userData);

        $studentData = $request->only(['my_class_id', 'section_id', 'adm_no']);
        $studentData['user_id'] = $newUser->id;
        $studentData['session'] = \App\Helpers\Qs::getSetting('current_session');
        $studentData['branch_id'] = $userData['branch_id'];

        $student = StudentRecord::create($studentData);

        return $this->successResponse($student->load(['user', 'my_class', 'section']), 'Student created successfully', 201);
    }
}
