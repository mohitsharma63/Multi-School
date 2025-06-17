<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Global system administrator with full access',
                'level' => 1,
                'status' => 1
            ],
            [
                'name' => 'school_admin',
                'display_name' => 'School Administrator',
                'description' => 'Administrator for a specific school',
                'level' => 2,
                'status' => 1
            ],
            [
                'name' => 'branch_admin',
                'display_name' => 'Branch Administrator',
                'description' => 'Administrator for a specific branch',
                'level' => 3,
                'status' => 1
            ],
            [
                'name' => 'teacher',
                'display_name' => 'Teacher',
                'description' => 'Teaching staff member',
                'level' => 4,
                'status' => 1
            ],
            [
                'name' => 'accountant',
                'display_name' => 'Accountant',
                'description' => 'Financial staff member',
                'level' => 4,
                'status' => 1
            ],
            [
                'name' => 'librarian',
                'display_name' => 'Librarian',
                'description' => 'Library staff member',
                'level' => 4,
                'status' => 1
            ],
            [
                'name' => 'parent',
                'display_name' => 'Parent',
                'description' => 'Student parent/guardian',
                'level' => 5,
                'status' => 1
            ],
            [
                'name' => 'student',
                'display_name' => 'Student',
                'description' => 'Student user',
                'level' => 5,
                'status' => 1
            ]
        ];

        foreach($roles as $role) {
            Role::create($role);
        }
    }
}
