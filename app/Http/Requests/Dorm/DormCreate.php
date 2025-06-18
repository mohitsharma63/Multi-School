<?php

namespace App\Http\Requests\Dorm;

use Illuminate\Foundation\Http\FormRequest;

class DormCreate extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'name' => 'required|string|max:100|unique:dorms',
            'school_id' => 'nullable|exists:schools,id',
            'description' => 'nullable|string|max:255',
        ];
    }

}
