<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('branches')->delete();
        $data = [
            [
                'name' => 'Main Campus',
                'code' => 'MAIN',
                'address' => '123 Education Street, City Center',
                'phone' => '+1234567890',
                'email' => 'main@school.edu',
                'head_name' => 'Dr. John Smith',
                'head_phone' => '+1234567891',
                'head_email' => 'john.smith@school.edu',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'North Branch',
                'code' => 'NORTH',
                'address' => '456 North Avenue, Northside',
                'phone' => '+1234567892',
                'email' => 'north@school.edu',
                'head_name' => 'Ms. Jane Doe',
                'head_phone' => '+1234567893',
                'head_email' => 'jane.doe@school.edu',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'South Branch',
                'code' => 'SOUTH',
                'address' => '789 South Boulevard, Southside',
                'phone' => '+1234567894',
                'email' => 'south@school.edu',
                'head_name' => 'Mr. Bob Johnson',
                'head_phone' => '+1234567895',
                'head_email' => 'bob.johnson@school.edu',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        DB::table('branches')->insert($data);
    }
}
