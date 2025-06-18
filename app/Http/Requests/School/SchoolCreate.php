<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class SchoolCreate extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'acronym' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_session' => 'nullable|string',
            'term_ends' => 'nullable|date',
            'term_begins' => 'nullable|date',
            'lock_exam' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }
}
