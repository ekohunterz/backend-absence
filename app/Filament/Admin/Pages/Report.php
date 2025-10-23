<?php

namespace App\Filament\Admin\Pages;

use App\Models\AcademicYear;
use App\Models\AttendanceDetail;
use App\Models\Grade;
use Filament\Pages\Page;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use UnitEnum;
use BackedEnum;

class Report extends Page
{
    protected string $view = 'filament.admin.pages.report';

    protected static ?string $navigationLabel = 'Rekap';

    protected static ?string $title = 'Rekap';

    protected static string|null|BackedEnum $navigationIcon = Phosphor::ChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Fitur';

    protected static ?int $navigationSort = 3;

    public ?int $grade_id = null;
    public ?int $month = null;
    public ?int $academic_year_id = null;

    public $academic_years;
    public $grades;
    public $reports = [];


    public function mount(): void
    {
        $this->grades = Grade::orderBy('name')->get();
        $this->month = (int) now()->translatedFormat('m');
        $this->academic_years = AcademicYear::all();
        $this->academic_year_id = $this->academic_years->where('is_active', true)->first()->id;

    }


    public function loadReport(): void
    {
        if (!$this->grade_id)
            return;

        $this->reports = AttendanceDetail::query()
            ->whereHas('attendance', function ($q) {
                $q->where('grade_id', $this->grade_id)
                    ->where('academic_year_id', $this->academic_year_id)
                    ->when($this->month, function ($q) {
                        $q->whereMonth('date', $this->month);
                    });
            })
            ->with(['student'])
            ->get()
            ->groupBy('student_id')
            ->map(function ($records) {
                $student = $records->first()->student;
                return [
                    'name' => $student->name,
                    'nis' => $student->nis,
                    'hadir' => $records->where('status', 'hadir')->count(),
                    'izin' => $records->where('status', 'izin')->count(),
                    'sakit' => $records->where('status', 'sakit')->count(),
                    'alpa' => $records->where('status', 'alpa')->count(),
                ];
            })
            ->values();
    }
}
