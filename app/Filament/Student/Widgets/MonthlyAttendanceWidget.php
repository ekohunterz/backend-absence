<?php

namespace App\Filament\Student\Widgets;

use App\Models\AttendanceDetail;
use App\Models\Student;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonthlyAttendanceWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $student = auth('student')->user();

        if (!$student) {
            return [];
        }

        // Get current month attendance
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();


        $attendances = AttendanceDetail::whereHas('attendance', function ($query) use ($student, $startOfMonth, $endOfMonth) {
            $query->where('grade_id', $student->grade_id)
                ->whereBetween('presence_date', [$startOfMonth, $endOfMonth]);
        })
            ->where('student_id', $student->id)
            ->get();

        // Count by status
        $hadir = $attendances->where('status', 'hadir')->count();
        $izin = $attendances->where('status', 'izin')->count();
        $sakit = $attendances->where('status', 'sakit')->count();
        $alpa = $attendances->where('status', 'alpa')->count();

        $totalCount = $hadir + $izin + $sakit + $alpa;
        // Calculate percentage
        $attendanceRate = $totalCount > 0 ? round(($hadir / $totalCount) * 100) : 0;

        return [
            Stat::make('Kehadiran Bulan Ini', $hadir . ' hari')
                ->description($attendanceRate . '% tingkat kehadiran')
                ->descriptionIcon('heroicon-o-check-circle')
                ->chart($this->getChartData($student, $startOfMonth))
                ->color($attendanceRate >= 80 ? 'success' : ($attendanceRate >= 60 ? 'warning' : 'danger')),

            Stat::make('Izin', $izin . ' hari')
                ->description('Izin dengan keterangan')
                ->descriptionIcon('heroicon-o-information-circle')
                ->color('warning'),

            Stat::make('Sakit', $sakit . ' hari')
                ->description('Sakit dengan keterangan')
                ->descriptionIcon('heroicon-o-heart')
                ->color('info'),

            Stat::make('Alpa', $alpa . ' hari')
                ->description($alpa > 0 ? 'Perlu ditingkatkan' : 'Pertahankan!')
                ->descriptionIcon($alpa > 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-badge')
                ->color($alpa > 0 ? 'danger' : 'success'),
        ];
    }

    protected function getChartData(Student $student, Carbon $startOfMonth): array
    {
        $chartData = [];
        $daysInMonth = min(now()->day, 30); // Max 30 days for chart

        for ($i = 0; $i < $daysInMonth; $i++) {
            $date = $startOfMonth->copy()->addDays($i);

            $attendance = AttendanceDetail::whereHas('attendance', function ($query) use ($student, $date) {
                $query->where('grade_id', $student->grade_id)
                    ->whereDate('presence_date', $date);
            })
                ->where('student_id', $student->id)
                ->first();

            $chartData[] = $attendance && $attendance->status === 'hadir' ? 1 : 0;
        }

        return $chartData;
    }


}