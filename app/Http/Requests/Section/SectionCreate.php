<?php

namespace App\Http\Requests\Section;

use App\Helpers\Qs;
use Illuminate\Foundation\Http\FormRequest;

class SectionCreate extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:1|max:100',
            'my_class_id' => 'required',
            'teacher_id' => 'sometimes|nullable',
            'school_id' => 'required|exists:schools,id',
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

    protected function getValidatorInstance()
    {
        $input = $this->all();

        $input['teacher_id'] = $input['teacher_id'] ? Qs::decodeHash($input['teacher_id']) : NULL;

        $this->getInputSource()->replace($input);

        return parent::getValidatorInstance();
    }

}
