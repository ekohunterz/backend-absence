<?php

namespace App\Filament\Admin\Pages;

use App\Exports\AttendanceGradeReportExport;
use App\Exports\AttendanceReportExport;
use App\Models\AcademicYear;
use App\Models\AttendanceDetail;
use App\Models\Grade;
use App\Models\Major;
use App\Models\Semester;
use App\Models\Student;
use App\Services\ReportService;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;
use BackedEnum;

class Report extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.admin.pages.report';

    protected static ?string $navigationLabel = 'Rekap';

    protected static ?string $title = 'Rekap';

    protected static string|null|BackedEnum $navigationIcon = Phosphor::ChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Fitur';

    protected static ?int $navigationSort = 4;

    public ?array $data = [];
    public $selectedSemesterId;
    public $selectedGradeId;
    public ?Semester $semester = null;
    public ?Grade $grade = null;
    public $reportData = [];
    public $statistics = [];
    public $maxDays = 31;

    public function mount(): void
    {
        // Get active semester
        $activeSemester = Semester::where('is_active', true)->first();
        $this->selectedSemesterId = $activeSemester?->id ?? Semester::first()?->id;

        // Get first grade
        $this->selectedGradeId = Grade::first()?->id;

        $this->form->fill([
            'semester' => $this->selectedSemesterId,
            'grade' => $this->selectedGradeId,
        ]);

        $this->loadReportData();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('semester')
                    ->label('Semester')
                    ->options(function () {
                        return Semester::with('academicYear')
                            ->get()
                            ->mapWithKeys(function ($semester) {
                                return [$semester->id => $semester->academicYear->name . ' - ' . $semester->name];
                            });
                    })
                    ->default($this->selectedSemesterId)
                    ->native(false)
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn($state) => $this->updateData($state, $this->selectedGradeId)),

                Select::make('grade')
                    ->label('Kelas')
                    ->options(Grade::orderBy('name')->pluck('name', 'id'))
                    ->default($this->selectedGradeId)
                    ->native(false)
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(fn($state) => $this->updateData($this->selectedSemesterId, $state)),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function updateData($semesterId, $gradeId): void
    {
        $this->selectedSemesterId = $semesterId;
        $this->selectedGradeId = $gradeId;

        $this->loadReportData();
    }

    protected function loadReportData(): void
    {
        $reportService = new ReportService($this->selectedSemesterId, $this->selectedGradeId);
        $this->reportData = $reportService->loadReportData()['reportData'];
        $this->statistics = $reportService->loadReportData()['statistics'];
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

    public function getSemesterLabel(): string
    {
        if (!$this->semester) {
            return '-';
        }

        return $this->semester->academicYear->name . ' - ' . $this->semester->name;
    }

    public function getGradeLabel(): string
    {
        return $this->grade?->name ?? '-';
    }

    public function exportExcel()
    {
        // TODO: Implement Excel export
        \Filament\Notifications\Notification::make()
            ->title('Export Excel')
            ->body('Fitur export akan segera tersedia')
            ->info()
            ->send();
    }

    public function exportPdf()
    {
        // TODO: Implement PDF export
        \Filament\Notifications\Notification::make()
            ->title('Export PDF')
            ->body('Fitur export akan segera tersedia')
            ->info()
            ->send();
    }

    public function print()
    {
        $this->js('window.print()');
    }

    public function exportToExcel()
    {
        $fileName = 'Laporan_Absensi_' . $this->grades->where('id', $this->grade_id)->first()->name . '_' . now()->format('F_Y') . '.xlsx';
        $academic_year = $this->academic_years->where('id', $this->academic_year_id)->first();
        return Excel::download(
            new AttendanceReportExport($this->reports, $this->grade_id, $this->month, $academic_year->name),
            $fileName
        );
    }

    public function exportGradeToExcel()
    {
        $fileName = 'Laporan_Absensi_' . now()->format('F_Y') . '.xlsx';
        $academic_year = $this->academic_years->where('id', $this->academic_year_id)->first();
        return Excel::download(
            new AttendanceGradeReportExport($this->report_grades, $this->month, $academic_year->name),
            $fileName
        );
    }

}
