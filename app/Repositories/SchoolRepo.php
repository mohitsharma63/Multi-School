<?php

namespace App\Repositories;

use App\Models\School;

class SchoolRepo
{
    public static function getAll()
    {
        return School::orderBy('name')->get();
    }

    public static function find($id)
    {
        return School::find($id);
    }

    public static function create($data)
    {
        return School::create($data);
    }

    public static function update($id, $data)
    {
        return School::where('id', $id)->update($data);
    }

    public static function delete($id)
    {
        return School::destroy($id);
    }

    public static function getActive()
    {
        return School::where('is_active', 1)->orderBy('name')->get();
    }
}
