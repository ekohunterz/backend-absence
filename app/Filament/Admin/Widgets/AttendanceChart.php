<?php

namespace App\Filament\Admin\Widgets;

use App\Models\AttendanceDetail;
use Filament\Widgets\ChartWidget;

class AttendanceChart extends ChartWidget
{
    protected ?string $heading = 'Rekap Absensi Hari Ini';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $today = now()->toDateString();

        // Ambil semua detail absensi hari ini
        $records = AttendanceDetail::whereHas(
            'attendance',
            fn($q) =>
            $q->whereDate('presence_date', $today)
        )->get();

        // Hitung total tiap status
        $totalHadir = $records->where('status', 'hadir')->count();
        $totalIzin = $records->where('status', 'izin')->count();
        $totalSakit = $records->where('status', 'sakit')->count();
        $totalAlpa = $records->where('status', 'alpa')->count();

        return [
            'labels' => ['Hadir', 'Izin', 'Sakit', 'Alpa'],
            'datasets' => [
                [
                    'label' => 'Rekap Kehadiran Hari Ini',
                    'data' => [
                        $totalHadir,
                        $totalIzin,
                        $totalSakit,
                        $totalAlpa,
                    ],
                    'backgroundColor' => [
                        'rgb(0, 64, 2)',
                        'rgb(255, 205, 86)',
                        'rgb(255, 99, 132)',
                        'rgb(156, 10, 0)',
                    ],

                ],
            ],
        ];
    }


    protected function getType(): string
    {
        return 'pie';
    }
}
