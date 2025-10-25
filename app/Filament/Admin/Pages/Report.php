<?php

namespace App\Filament\Admin\Pages;

use App\Exports\AttendanceGradeReportExport;
use App\Exports\AttendanceReportExport;
use App\Models\AcademicYear;
use App\Models\AttendanceDetail;
use App\Models\Grade;
use App\Models\Major;
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

    public ?array $data = [];
    protected string $view = 'filament.admin.pages.report';

    protected static ?string $navigationLabel = 'Rekap';

    protected static ?string $title = 'Rekap';

    protected static string|null|BackedEnum $navigationIcon = Phosphor::ChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Fitur';

    protected static ?int $navigationSort = 4;

    public ?string $activeTab = 'tab1';

    public ?int $grade_id = null;
    public ?int $month = null;

    public ?int $major_id = null;

    public ?int $academic_year_id = null;

    public $academic_years;
    public $grades;
    public $majors;
    public $reports = [];

    public $report_grades = [];


    public function mount(): void
    {
        $this->grades = Grade::orderBy('name')->get();
        $this->majors = Major::all();
        $this->month = (int) now()->translatedFormat('m');
        $this->academic_years = AcademicYear::all();
        $this->academic_year_id = $this->academic_years->where('is_active', true)->first()->id;
        $this->loadReportGrades();
    }

    protected function getAcademicYearFormComponent(): Component
    {
        return Select::make('academic_year_id')
            ->hiddenLabel()
            ->placeholder('Pilih Tahun Ajaran')
            ->options(
                $this->academic_years
                    ->pluck('name', 'id')
                    ->toArray()
            )
            ->searchable()
            ->afterStateUpdated(fn($state) => $this->academic_year_id = $state);
    }

    protected function getMonthFormComponents(): Component
    {
        return Select::make('month')
            ->hiddenLabel()
            ->placeholder('Tampilkan Semua')
            ->options(
                ['' => 'Tampilkan Semua'] +
                collect(range(1, 12))
                    ->mapWithKeys(fn($m) => [
                        $m => Carbon::create()->month($m)->translatedFormat('F'),
                    ])
                    ->toArray()
            )
            ->searchable()
            ->default(now()->month) // otomatis bulan saat ini
            ->afterStateUpdated(fn($state) => $this->month = $state);

    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('grade_id')
                    ->hiddenLabel()
                    ->placeholder('Pilih Kelas')
                    ->options(
                        $this->grades
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->afterStateUpdated(fn($state) => $this->grade_id = $state), // sinkronkan ke variabel
                $this->getAcademicYearFormComponent(),
                $this->getMonthFormComponents(),
            ])->columns(3);
    }

    public function form_major(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('major_id')
                    ->hiddenLabel()
                    ->placeholder('Pilih Jurusan')
                    ->options(
                        $this->majors
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->searchable()
                    ->afterStateUpdated(fn($state) => $this->major_id = $state),
                $this->getAcademicYearFormComponent(),
                $this->getMonthFormComponents(),
            ])->columns(3);
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

    public function loadReportGrades(): void
    {
        $this->report_grades = AttendanceDetail::query()
            ->whereHas('attendance', function ($q) {
                $q->where('academic_year_id', $this->academic_year_id)
                    ->when($this->month, function ($q) {
                        $q->whereMonth('date', $this->month);
                    })->when($this->major_id, function ($q) {
                        $q->whereHas('grade', function ($q) {
                            $q->where('major_id', $this->major_id);
                        });

                    });
            })
            ->with(['attendance.grade'])
            ->get()
            ->groupBy('attendance.grade_id')
            ->map(function ($records) {
                $grade = $records->first()->attendance->grade;
                return [
                    'name' => $grade->name,
                    'hadir' => $records->where('status', 'hadir')->count(),
                    'izin' => $records->where('status', 'izin')->count(),
                    'sakit' => $records->where('status', 'sakit')->count(),
                    'alpa' => $records->where('status', 'alpa')->count(),
                ];
            })
            ->values();
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
