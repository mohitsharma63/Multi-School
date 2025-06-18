<?php

namespace App\Repositories;

use App\Models\School;

class SchoolRepo
{
    public function getAll()
    {
        return School::orderBy('name')->get();
    }

    public function find($id)
    {
        return School::find($id);
    }

    public function create($data)
    {
        return School::create($data);
    }

    public function update($id, $data)
    {
        return School::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return School::destroy($id);
    }

    public function getActive()
    {
        return School::where('is_active', 1)->orderBy('name')->get();
    }
}
