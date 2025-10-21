<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = [
            ['name' => 'Rekayasa Perangkat Lunak', 'code' => 'RPL'],
            ['name' => 'Teknik Komputer Jaringan', 'code' => 'TKJ'],
            ['name' => 'Akuntansi', 'code' => 'AKL'],
            ['name' => 'Otomatisasi dan Tata Kelola Perkantoran', 'code' => 'OTKP'],
            ['name' => 'Teknik Mesin Industri', 'code' => 'TMI'],
            ['name' => 'Teknik Kendaraan Ringan', 'code' => 'TKR'],
            ['name' => 'Teknik Otomasi Industri', 'code' => 'TOI'],
            ['name' => 'Teknik Elektronika Industri', 'code' => 'TEI'],
        ];

        foreach ($majors as $major) {
            Major::create($major);
        }
    }
}
