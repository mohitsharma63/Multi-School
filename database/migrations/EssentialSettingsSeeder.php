<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class EssentialSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'type' => 'current_session',
                'description' => date('Y') . '-' . (date('Y') + 1),
            ],
            [
                'type' => 'system_title',
                'description' => 'Indian Multi-School Management System',
            ],
            [
                'type' => 'system_name',
                'description' => 'Indian School System',
            ],
            [
                'type' => 'current_school_id',
                'description' => '1',
            ],
            [
                'type' => 'app_code',
                'description' => 'IND',
            ],
            [
                'type' => 'currency',
                'description' => 'INR',
            ],
            [
                'type' => 'currency_symbol',
                'description' => '₹',
            ]
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['type' => $setting['type']],
                ['description' => $setting['description']]
            );
        }
    }
}
