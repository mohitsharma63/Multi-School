<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class ExamCreate extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:50',
            'term' => 'required|string',
            'school_id' => 'required|exists:schools,id',
        ];
    }

}
