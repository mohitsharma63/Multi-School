<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class BranchUpdate extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->isSuperAdmin() || auth()->user()->isSchoolAdmin();
    }

    public function rules()
    {
        return [
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code,' . $this->route('branch'),
            'location' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'status' => 'required|boolean'
        ];
    }
}
