<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'school_name' => 'SMK Negeri 1 Contoh',
            'school_address' => 'Jl. Pendidikan No. 10, Kota Contoh',
            'school_phone' => '08123456789',
            'school_email' => '6oEoG@example.com',
            'latitude' => -6.2000000,
            'longitude' => 106.8166667,
            'radius' => 100, // in meters
            'start_time' => '07:00:00',
            'end_time' => '15:00:00',
        ]);
    }
}
