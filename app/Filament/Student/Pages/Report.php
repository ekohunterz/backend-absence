<?php

namespace App\Filament\Student\Pages;

use App\Models\AcademicYear;
use App\Models\AttendanceDetail;
use App\Models\Semester;
use Carbon\Carbon;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;
use Illuminate\Database\Eloquent\Collection;

class Report extends Page
{
    protected string $view = 'filament.student.pages.report';

    protected static ?string $title = 'Rekap';

    protected static string|null|BackedEnum $navigationIcon = Phosphor::ChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 4;

    public ?array $data = [];
    public $selectedAcademicYearId;
    public $selectedSemesterId;
    public ?AcademicYear $academicYear = null;
    public ?Semester $semester = null;
    public $semesterData = [];
    public $statistics = [];
    public $maxDays = 31;

    public function mount(): void
    {
        // Get active semester or first available
        $activeSemester = Semester::where('is_active', true)->first();

        if ($activeSemester) {
            $this->selectedSemesterId = $activeSemester->id;
            $this->selectedAcademicYearId = $activeSemester->academic_year_id;
        } else {
            // Fallback to first semester
            $firstSemester = Semester::first();
            if ($firstSemester) {
                $this->selectedSemesterId = $firstSemester->id;
                $this->selectedAcademicYearId = $firstSemester->academic_year_id;
            }
        }

        $this->form->fill([
            'academic_year' => $this->selectedAcademicYearId,
            'semester' => $this->selectedSemesterId,
        ]);

        $this->loadSemesterData();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('academic_year')
                    ->label('Tahun Ajaran')
                    ->options(AcademicYear::orderBy('start_date', 'desc')->pluck('name', 'id'))
                    ->default($this->selectedAcademicYearId)
                    ->native(false)
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        // Reset semester when academic year changes
                        $firstSemester = Semester::where('academic_year_id', $state)->first();
                        $this->selectedSemesterId = $firstSemester?->id;
                        $this->updateData($state, $this->selectedSemesterId);
                    }),

                Select::make('semester')
                    ->label('Semester')
                    ->options(function () {
                        if (!$this->selectedAcademicYearId) {
                            return [];
                        }
                        return Semester::where('academic_year_id', $this->selectedAcademicYearId)
                            ->pluck('name', 'id');
                    })
                    ->default($this->selectedSemesterId)
                    ->native(false)
                    ->reactive()
                    ->afterStateUpdated(fn($state) => $this->updateData($this->selectedAcademicYearId, $state)),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function updateData($academicYearId, $semesterId): void
    {
        $this->selectedAcademicYearId = $academicYearId;
        $this->selectedSemesterId = $semesterId;
        $this->loadSemesterData();
    }

    protected function loadSemesterData(): void
    {
        $student = auth('student')->user();

        if (!$student || !$this->selectedAcademicYearId || !$this->selectedSemesterId) {
            $this->resetData();
            return;
        }

        // Get academic year and semester data
        $this->academicYear = AcademicYear::find($this->selectedAcademicYearId);
        $this->semester = Semester::find($this->selectedSemesterId);

        if (!$this->academicYear || !$this->semester) {
            $this->resetData();
            return;
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
            ];
            $currentMonth->addMonth();
        }

        // Get all attendance data for the semester
        $attendances = AttendanceDetail::whereHas('attendance', function ($query) use ($student) {
            $query->where('grade_id', $student->grade_id)
                ->where('semester_id', $this->selectedSemesterId);
        })
            ->where('student_id', $student->id)
            ->with(['attendance'])
            ->get();

        // Group by month and day
        $groupedData = $attendances->groupBy(function ($item) {
            return Carbon::parse($item->attendance->presence_date)->format('Y-m');
        })->map(function ($monthData) {
            return $monthData->keyBy(function ($item) {
                return Carbon::parse($item->attendance->presence_date)->day;
            });
        });

        // Build semester data structure
        $this->semesterData = [];
        $totalStats = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'total_days' => 0,
        ];

        foreach ($months as $monthInfo) {
            $year = $monthInfo['year'];
            $month = $monthInfo['month'];

            $date = Carbon::create($year, $month, 1);
            $daysInMonth = $date->daysInMonth;
            $monthKey = $date->format('Y-m');

            $monthData = [
                'month' => $month,
                'month_name' => $date->locale('id')->translatedFormat('F'),
                'year' => $year,
                'days_in_month' => $daysInMonth,
                'days' => [],
                'stats' => [
                    'hadir' => 0,
                    'sakit' => 0,
                    'izin' => 0,
                    'alpa' => 0,
                ],
            ];

            // Fill days data
            for ($day = 1; $day <= $this->maxDays; $day++) {
                if ($day <= $daysInMonth) {
                    $currentDate = Carbon::create($year, $month, $day);

                    // Only process if date is within semester range
                    if ($currentDate >= $startDate && $currentDate <= $endDate) {
                        $attendance = $groupedData->get($monthKey)?->get($day);

                        $status = $attendance ? $attendance->status : null;

                        // Count statistics
                        if ($status) {
                            $monthData['stats'][$status] = ($monthData['stats'][$status] ?? 0) + 1;
                            $totalStats[$status]++;
                        }

                        $monthData['days'][$day] = [
                            'date' => $currentDate,
                            'status' => $status,
                            'check_in' => $attendance ? $attendance->check_in_time : null,
                            'check_out' => $attendance ? $attendance->check_out_time : null,
                            'is_weekend' => $currentDate->isWeekend(),
                        ];
                    } else {
                        // Date outside semester range
                        $monthData['days'][$day] = null;
                    }
                } else {
                    // Empty cell for days beyond month length
                    $monthData['days'][$day] = null;
                }
            }

            $this->semesterData[] = $monthData;
        }

        // Calculate overall statistics
        $totalCount = $totalStats['hadir'] + $totalStats['sakit'] + $totalStats['izin'] + $totalStats['alpa'];

        $this->statistics = [
            'hadir' => $totalStats['hadir'],
            'sakit' => $totalStats['sakit'],
            'izin' => $totalStats['izin'],
            'alpa' => $totalStats['alpa'],
            'total_days' => $totalCount,
            'attendance_rate' => $totalCount > 0
                ? round(($totalStats['hadir'] / $totalCount) * 100, 1)
                : 0,
        ];
    }

    protected function resetData(): void
    {
        $this->semesterData = [];
        $this->statistics = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'total_days' => 0,
            'attendance_rate' => 0,
        ];
    }

    public function getStatusColor(?string $status): string
    {
        if (!$status) {
            return 'bg-gray-100 dark:bg-gray-800';
        }

        return match ($status) {
            'hadir' => 'bg-green-500',
            'sakit' => 'bg-yellow-500',
            'izin' => 'bg-blue-500',
            'alpa' => 'bg-red-500',
            default => 'bg-gray-200 dark:bg-gray-700',
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

    public function getStatusTooltip(?string $status, $data): string
    {
        if (!$status || !$data) {
            return '';
        }

        $tooltip = ucfirst($status);
        if ($data['check_in']) {
            $tooltip .= "\nMasuk: " . Carbon::parse($data['check_in'])->format('H:i');
        }
        if ($data['check_out']) {
            $tooltip .= "\nKeluar: " . Carbon::parse($data['check_out'])->format('H:i');
        }

        return $tooltip;
    }

    public function getAcademicYearLabel(): string
    {
        if (!$this->academicYear) {
            return '-';
        }

        return $this->academicYear->name;
    }

    public function getSemesterLabel(): string
    {
        if (!$this->semester) {
            return '-';
        }

        return $this->semester->name;
    }

    public function getSemesterPeriod(): string
    {
        if (!$this->semester) {
            return '-';
        }

        $start = Carbon::parse($this->semester->start_date)->locale('id')->translatedFormat('d F Y');
        $end = Carbon::parse($this->semester->end_date)->locale('id')->translatedFormat('d F Y');

        return "{$start} - {$end}";
    }

}
