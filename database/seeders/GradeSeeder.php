<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = Major::all();

        foreach ($majors as $major) {
            Grade::create([
                'name' => 'X ' . $major->code . ' 1',
                'major_id' => $major->id,
            ]);

            Grade::create([
                'name' => 'XI ' . $major->code . ' 1',
                'major_id' => $major->id,
            ]);

            Grade::create([
                'name' => 'XII ' . $major->code . ' 1',
                'major_id' => $major->id,
            ]);
        }
    }
}
