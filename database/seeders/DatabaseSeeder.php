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

        $this->call(ShieldSeeder::class);
        User::factory(8)->create()->each(function (User $user) {
            $user->assignRole('guru');
        });
        User::factory(2)->create()->each(function (User $user) {
            $user->assignRole('operator');
        });
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'nip' => '123456789',
            'gender' => 'L',
        ])->assignRole('super_admin');

        $this->call([
            MajorSeeder::class,
            GradeSeeder::class,
            AcademicYearSeeder::class,
            SemesterSeeder::class,
            StudentSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
