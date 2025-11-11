<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $academicYears = AcademicYear::all();

        foreach ($academicYears as $academicYear) {
            // Extract year from academic year name (e.g., "2024/2025" -> 2024)
            $startYear = (int) explode('/', $academicYear->name)[0];

            // Semester 1 (Ganjil): Juli - Desember
            Semester::create([
                'academic_year_id' => $academicYear->id,
                'name' => 'Semester 1 (Ganjil)',
                'semester' => 1,
                'start_date' => "{$startYear}-07-01",
                'end_date' => "{$startYear}-12-31",
                'is_active' => $academicYear->is_active && now()->month >= 7,
            ]);

            // Semester 2 (Genap): Januari - Juni
            Semester::create([
                'academic_year_id' => $academicYear->id,
                'name' => 'Semester 2 (Genap)',
                'semester' => 2,
                'start_date' => ($startYear + 1) . "-01-01",
                'end_date' => ($startYear + 1) . "-06-30",
                'is_active' => $academicYear->is_active && now()->month <= 6,
            ]);
        }
    }
}