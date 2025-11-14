<?php

namespace App\Filament\Admin\Pages;

use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Major;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use UnitEnum;
use BackedEnum;



class Presence extends Page implements HasSchemas
{
    use InteractsWithSchemas;
    protected string $view = 'filament.admin.pages.presence';

    protected static ?string $navigationLabel = 'Presensi';

    protected static ?string $title = 'Presensi';

    protected static string|null|BackedEnum $navigationIcon = Phosphor::MapPinArea;

    protected static string|UnitEnum|null $navigationGroup = 'Fitur';

    public $grades = [];
    public ?string $selected_date = null;
    public $filterStatus = 'all';
    public ?string $search = null;

    public function mount(): void
    {
        $this->selected_date = now()->format('Y-m-d');
        $this->loadGrades();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('selected_date')
                ->label('Tanggal')
                ->default($this->selected_date)
                ->maxDate(now()->format('Y-m-d'))
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    $this->selected_date = $state ?: now()->format('Y-m-d');
                    $this->loadGrades();
                }),
            TextInput::make('search')
                ->label('Cari Kelas')
                ->placeholder('Cari Kelas')
                ->reactive()
                ->debounce(500)
                ->afterStateUpdated(function ($state) {
                    $this->search = $state;
                    $this->loadGrades();
                })
        ])->columns(2);
    }

    public function loadGrades(): void
    {
        $selectedDate = $this->selected_date ?: now()->format('Y-m-d');

        $this->grades = Grade::with(['major'])
            ->withCount([
                'students' => function ($query) {
                    $query->where('status', 'aktif');
                }
            ])
            ->when($this->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get()
            ->map(function ($grade) use ($selectedDate) {
                // Check if attendance exists for this grade on selected date
                $hasAttendance = Attendance::where('grade_id', $grade->id)
                    ->whereDate('presence_date', $selectedDate)
                    ->whereNotNull('verified_at')
                    ->exists();

                return [
                    'id' => $grade->id,
                    'name' => $grade->name,
                    'major' => [
                        'id' => $grade->major->id,
                        'name' => $grade->major->name,
                    ],
                    'students_count' => $grade->students_count,
                    'has_attendance_today' => $hasAttendance,
                ];
            })
            ->toArray();
    }

    public function updatedFilterStatus(): void
    {
        // Filter akan dihandle di view
        // Method ini untuk trigger reactivity
    }

    public function getTotalGradesProperty(): int
    {
        return count($this->grades);
    }

    public function getAttendedGradesProperty(): int
    {
        return collect($this->grades)->where('has_attendance_today', true)->count();
    }

    public function getNotAttendedGradesProperty(): int
    {
        return $this->totalGrades - $this->attendedGrades;
    }

    public function getCompletionPercentageProperty(): float
    {
        if ($this->totalGrades === 0) {
            return 0;
        }
        return round(($this->attendedGrades / $this->totalGrades) * 100, 1);
    }



}
