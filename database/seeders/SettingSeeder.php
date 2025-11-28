<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('settings')->insert([
            'user_id' => 1,
            'job_name' => 'Mcdonald',
            'salary_per_hour' => 7.83,
            'contract_hour' => 90,
            'night_hours_start' => '22:00',
            'night_hours_end' => '06:00',
            'night_salary' => 1.94,
            'salary_extra_hours' => 10.59,
            'extra_salary' => 160.14,
            'tax_percentage' => 8.48,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
