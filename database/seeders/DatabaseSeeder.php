<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
        ]);

        $this->call([
            MajorSeeder::class,
            GradeSeeder::class,
            AcademicYearSeeder::class,
            StudentSeeder::class,
            SettingSeeder::class,
            TeacherSeeder::class,
        ]);
    }
}
