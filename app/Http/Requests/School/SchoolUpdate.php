<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class SchoolUpdate extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->isSuperAdmin();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:schools,name,' . $this->route('school'),
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string',
            'academic_year' => 'required|string|max:20',
            'status' => 'required|boolean'
        ];
    }
}
