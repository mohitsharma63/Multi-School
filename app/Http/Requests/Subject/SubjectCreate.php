<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;

class SubjectCreate extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:50',
            'slug' => 'nullable|string|min:1|max:10',
            'school_id' => 'required|exists:schools,id',
            'my_class_id' => 'required',
            'teacher_id' => 'required',
        ];
    }

    public function attributes()
    {
        return  [
            'school_id' => 'School',
            'my_class_id' => 'Class',
            'teacher_id' => 'Teacher',
        ];
    }

}
