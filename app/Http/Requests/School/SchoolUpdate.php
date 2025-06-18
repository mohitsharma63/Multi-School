<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class SchoolUpdate extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $schoolId = $this->route('school')->id;

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:schools,code,' . $schoolId,
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'system_title' => 'nullable|string|max:255',
            'current_session' => 'nullable|string|max:20',
            'term_begins' => 'nullable|date',
            'term_ends' => 'nullable|date',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
