<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AcademicYear::create([
            'name' => '2025/2026',
            'is_active' => true,
            'start_date' => '2025-07-14',
            'end_date' => '2026-06-19',
        ]);

        AcademicYear::create([
            'name' => '2026/2027',
            'is_active' => false,
            'start_date' => '2026-07-14',
            'end_date' => '2027-06-19',
        ]);


    }
}
