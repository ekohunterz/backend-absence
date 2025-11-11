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
                        '#4caf50',
                        '#03a9f4',
                        '#ff9800',
                        '#ef5350',
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
