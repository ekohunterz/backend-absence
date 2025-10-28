<?php

namespace App\Filament\Admin\Pages;

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

    public $grades;
    public $majors;
    public ?int $major_id = null;
    public ?string $search = '';
    public ?string $presence_date = null;


    public function mount(): void
    {
        $this->majors = Major::orderBy('name')->get();
        $this->presence_date = now()->format('Y-m-d');
        $this->loadGrades();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 🔹 Tanggal Presensi
                DatePicker::make('presence_date')
                    ->hiddenLabel()
                    ->placeholder('Tanggal Presensi')
                    ->default($this->presence_date)
                    ->live()
                    ->reactive()
                    ->maxDate(now()->format('Y-m-d'))
                    ->afterStateUpdated(function ($state) {
                        $this->presence_date = $state ?? now()->format('Y-m-d');
                        $this->loadGrades();
                    }),

                // 🔹 Pencarian kelas
                TextInput::make('search')
                    ->hiddenLabel()
                    ->placeholder('Cari Kelas')
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($state) {
                        $this->search = $state ?? '';
                        $this->loadGrades();
                    }),

                // 🔹 Filter jurusan
                Select::make('major_id')
                    ->hiddenLabel()
                    ->placeholder('Semua Jurusan')
                    ->selectablePlaceholder(true)
                    ->options(
                        $this->majors->pluck('name', 'id')->toArray()
                    )
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $this->major_id = $state;
                        $this->loadGrades();
                    }),
            ])
            ->columns(3)
        ;
    }

    protected function loadGrades(): void
    {

        $this->grades = Grade::with(['major'])
            ->withExists([
                'attendances as has_attendance_today' => fn($query) =>
                    $query->whereDate('presence_date', $this->presence_date)->whereNotNull('verified_by'),
            ])
            ->when(
                filled($this->search),
                fn($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
            )
            ->when(
                $this->major_id,
                fn($q) =>
                $q->where('major_id', $this->major_id)
            )
            ->orderBy('name')
            ->get();
    }



}
