<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class BranchCreate extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:branches,code',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'head_name' => 'nullable|string|max:100',
            'head_phone' => 'nullable|string|max:20',
            'head_email' => 'nullable|email|max:100',
            'is_active' => 'required|boolean'
        ];
    }
}
