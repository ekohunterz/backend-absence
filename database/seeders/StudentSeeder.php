<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 1 sample manually
        Student::create([
            'nis' => '2025001',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'gender' => 'L',
            'birth_date' => '2009-05-14',
            'phone' => '08123456789',
            'password' => Hash::make('password'),
            'status' => 'aktif',
            'grade_id' => 1,
        ]);

        // Create 50 random students using factory
        Student::factory(50)->create();
    }
}
