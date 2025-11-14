<?php

namespace App\Services;

use App\Models\AttendanceDetail;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    protected int $maxDays = 31;

    public function generateReport(int $semesterId, int $gradeId): array
    {
        $semester = Semester::with('academicYear')->find($semesterId);
        $grade = Grade::find($gradeId);

        if (!$semester || !$grade) {
            return $this->emptyReport();
        }

        // Parse semester dates
        $startDate = Carbon::parse($semester->start_date);
        $endDate = Carbon::parse($semester->end_date);

        // Get all months in semester
        $months = $this->getMonthsInRange($startDate, $endDate);

        // Get all students in this grade
        $students = Student::where('grade_id', $gradeId)
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        // Get all attendance data
        $attendances = $this->getAttendances($semesterId, $gradeId, $students);

        // Group by student and date
        $groupedData = $this->groupAttendances($attendances);

        // Build report data
        return $this->buildReportData($semester, $grade, $months, $students, $groupedData, $startDate, $endDate);
    }

    protected function getMonthsInRange(Carbon $startDate, Carbon $endDate): array
    {
        $months = [];
        $currentMonth = $startDate->copy()->startOfMonth();

        while ($currentMonth <= $endDate) {
            $months[] = [
                'year' => $currentMonth->year,
                'month' => $currentMonth->month,
                'month_name' => $currentMonth->locale('id')->translatedFormat('F'),
                'month_name_short' => $currentMonth->locale('id')->translatedFormat('M'),
            ];
            $currentMonth->addMonth();
        }

        return $months;
    }

    protected function getAttendances(int $semesterId, int $gradeId, Collection $students): Collection
    {
        return AttendanceDetail::whereHas('attendance', function ($query) use ($semesterId, $gradeId) {
            $query->where('grade_id', $gradeId)
                ->where('semester_id', $semesterId);
        })
            ->whereIn('student_id', $students->pluck('id'))
            ->with(['attendance'])
            ->get();
    }

    protected function groupAttendances(Collection $attendances): Collection
    {
        return $attendances->groupBy('student_id')->map(function ($studentAttendances) {
            return $studentAttendances->groupBy(function ($item) {
                return Carbon::parse($item->attendance->presence_date)->format('Y-m');
            })->map(function ($monthData) {
                return $monthData->keyBy(function ($item) {
                    return Carbon::parse($item->attendance->presence_date)->day;
                });
            });
        });
    }

    protected function buildReportData(
        Semester $semester,
        Grade $grade,
        array $months,
        Collection $students,
        Collection $groupedData,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $reportData = [
            'semester' => $semester,
            'grade' => $grade,
            'months' => $months,
            'students' => [],
        ];

        $totalStats = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'total_students' => $students->count(),
        ];

        foreach ($students as $student) {
            $studentData = $this->buildStudentData($student, $months, $groupedData, $startDate, $endDate, $totalStats);
            $reportData['students'][] = $studentData;
        }

        $reportData['statistics'] = $totalStats;

        return $reportData;
    }

    protected function buildStudentData(
        Student $student,
        array $months,
        Collection $groupedData,
        Carbon $startDate,
        Carbon $endDate,
        array &$totalStats
    ): array {
        $studentData = [
            'id' => $student->id,
            'name' => $student->name,
            'nis' => $student->nis,
            'months' => [],
            'stats' => [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0,
            ],
        ];

        $studentAttendances = $groupedData->get($student->id) ?? collect();

        foreach ($months as $monthInfo) {
            $monthData = $this->buildMonthData($monthInfo, $studentAttendances, $studentData['stats'], $totalStats, $startDate, $endDate);
            $studentData['months'][] = $monthData;
        }

        return $studentData;
    }

    protected function buildMonthData(
        array $monthInfo,
        Collection $studentAttendances,
        array &$studentStats,
        array &$totalStats,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $year = $monthInfo['year'];
        $month = $monthInfo['month'];
        $date = Carbon::create($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        $monthKey = $date->format('Y-m');

        $monthData = ['days' => []];

        for ($day = 1; $day <= $this->maxDays; $day++) {
            if ($day <= $daysInMonth) {
                $currentDate = Carbon::create($year, $month, $day);

                if ($currentDate >= $startDate && $currentDate <= $endDate) {
                    $attendance = $studentAttendances->get($monthKey)?->get($day);
                    $status = $attendance ? $attendance->status : null;

                    if ($status) {
                        $studentStats[$status]++;
                        $totalStats[$status]++;
                    }

                    $monthData['days'][$day] = [
                        'date' => $currentDate,
                        'status' => $status,
                        'is_weekend' => $currentDate->isWeekend(),
                        'check_in' => $attendance ? $attendance->check_in_time : null,
                        'check_out' => $attendance ? $attendance->check_out_time : null,
                    ];
                } else {
                    $monthData['days'][$day] = null;
                }
            } else {
                $monthData['days'][$day] = null;
            }
        }

        return $monthData;
    }

    protected function emptyReport(): array
    {
        return [
            'semester' => null,
            'grade' => null,
            'months' => [],
            'students' => [],
            'statistics' => [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0,
                'total_students' => 0,
            ],
        ];
    }

    public function getStatusColor(?string $status): string
    {
        if (!$status) {
            return 'bg-gray-100';
        }

        return match ($status) {
            'hadir' => 'bg-green-500',
            'sakit' => 'bg-yellow-500',
            'izin' => 'bg-blue-500',
            'alpa' => 'bg-red-500',
            default => 'bg-gray-200',
        };
    }

    public function getStatusLabel(?string $status): string
    {
        if (!$status) {
            return '-';
        }

        return match ($status) {
            'hadir' => 'H',
            'sakit' => 'S',
            'izin' => 'I',
            'alpa' => 'A',
            default => '-',
        };
    }



}