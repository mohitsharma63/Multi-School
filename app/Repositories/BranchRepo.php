<?php

namespace App\Repositories;

use App\Models\Branch;

class BranchRepo
{
    public function all()
    {
        return Branch::orderBy('name')->get();
    }

    public function getActive()
    {
        return Branch::where('is_active', true)->orderBy('name')->get();
    }

    public function find($id)
    {
        return Branch::find($id);
    }

    public function create($data)
    {
        return Branch::create($data);
    }

    public function update($id, $data)
    {
        return Branch::find($id)->update($data);
    }

    public function delete($id)
    {
        return Branch::destroy($id);
    }
}
