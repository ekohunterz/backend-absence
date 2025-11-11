<?php

namespace App\Services;

use App\Models\AttendanceDetail;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\Student;
use Carbon\Carbon;

class ReportService
{
    public $selectedSemesterId;
    public $selectedGradeId;
    public ?Semester $semester = null;
    public ?Grade $grade = null;
    public $reportData = [];
    public $statistics = [];
    public $maxDays = 31;

    public function __construct($selectedSemesterId, $selectedGradeId)
    {
        $this->resetData();
        $this->selectedSemesterId = $selectedSemesterId;
        $this->selectedGradeId = $selectedGradeId;
    }


    public function loadReportData(): array
    {
        if (!$this->selectedSemesterId || !$this->selectedGradeId) {
            $this->resetData();
            return [];
        }

        // Get semester and grade
        $this->semester = Semester::with('academicYear')->find($this->selectedSemesterId);
        $this->grade = Grade::find($this->selectedGradeId);

        if (!$this->semester || !$this->grade) {
            $this->resetData();
            return [];
        }

        // Parse semester dates
        $startDate = Carbon::parse($this->semester->start_date);
        $endDate = Carbon::parse($this->semester->end_date);

        // Get all months in semester
        $months = [];
        $currentMonth = $startDate->copy()->startOfMonth();

        while ($currentMonth <= $endDate) {
            $months[] = [
                'year' => $currentMonth->year,
                'month' => $currentMonth->month,
                'month_name' => $currentMonth->locale('id')->translatedFormat('F'),
            ];
            $currentMonth->addMonth();
        }

        // Get all students in this grade
        $students = Student::where('grade_id', $this->selectedGradeId)
            ->orderBy('name')
            ->get();

        // Get all attendance data for this semester and grade
        $attendances = AttendanceDetail::whereHas('attendance', function ($query) {
            $query->where('grade_id', $this->selectedGradeId)
                ->where('semester_id', $this->selectedSemesterId);
        })
            ->whereIn('student_id', $students->pluck('id'))
            ->with(['attendance'])
            ->get();

        // Group by student and date
        $groupedData = $attendances->groupBy('student_id')->map(function ($studentAttendances) {
            return $studentAttendances->groupBy(function ($item) {
                return Carbon::parse($item->attendance->presence_date)->format('Y-m');
            })->map(function ($monthData) {
                return $monthData->keyBy(function ($item) {
                    return Carbon::parse($item->attendance->presence_date)->day;
                });
            });
        });

        // Build report data
        $this->reportData = [
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
                $year = $monthInfo['year'];
                $month = $monthInfo['month'];
                $date = Carbon::create($year, $month, 1);
                $daysInMonth = $date->daysInMonth;
                $monthKey = $date->format('Y-m');

                $monthData = [
                    'days' => [],
                ];

                // Fill days data
                for ($day = 1; $day <= $this->maxDays; $day++) {
                    if ($day <= $daysInMonth) {
                        $currentDate = Carbon::create($year, $month, $day);

                        // Only process if date is within semester range
                        if ($currentDate >= $startDate && $currentDate <= $endDate) {
                            $attendance = $studentAttendances->get($monthKey)?->get($day);
                            $status = $attendance ? $attendance->status : null;

                            // Count statistics
                            if ($status) {
                                $studentData['stats'][$status]++;
                                $totalStats[$status]++;
                            }

                            $monthData['days'][$day] = [
                                'date' => $currentDate,
                                'status' => $status,
                                'is_weekend' => $currentDate->isWeekend(),
                            ];
                        } else {
                            $monthData['days'][$day] = null;
                        }
                    } else {
                        $monthData['days'][$day] = null;
                    }
                }

                $studentData['months'][] = $monthData;
            }

            $this->reportData['students'][] = $studentData;
        }

        // Calculate statistics
        $this->statistics = $totalStats;

        return [
            'reportData' => $this->reportData,
            'statistics' => $this->statistics,
        ];
    }

    protected function resetData(): void
    {
        $this->reportData = [
            'months' => [],
            'students' => [],
        ];
        $this->statistics = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'total_students' => 0,
        ];
    }



}