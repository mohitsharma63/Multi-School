<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// API routes with branch isolation
Route::middleware(['auth:api', 'api.branch.isolation'])->group(function () {
    Route::apiResource('students', 'Api\StudentController');
    Route::apiResource('payments', 'Api\PaymentController');
    Route::apiResource('classes', 'Api\ClassController');
    Route::apiResource('subjects', 'Api\SubjectController');
    Route::apiResource('exams', 'Api\ExamController');
    
    // Branch and school info for Super Admin
    Route::get('schools', 'Api\SchoolController@index');
    Route::get('branches', 'Api\BranchController@index');
});
