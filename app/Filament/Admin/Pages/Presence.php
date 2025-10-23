<?php

namespace App\Filament\Admin\Pages;

use App\Models\Grade;
use App\Models\Major;
use Filament\Pages\Page;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use UnitEnum;
use BackedEnum;



class Presence extends Page
{
    protected string $view = 'filament.admin.pages.presence';

    protected static ?string $navigationLabel = 'Presensi';

    protected static ?string $title = 'Presensi Hari Ini';

    protected static string|null|BackedEnum $navigationIcon = Phosphor::MapPinArea;

    protected static string|UnitEnum|null $navigationGroup = 'Fitur';

    public $grades;
    public $majors;
    public string $search = '';
    public ?int $selectedMajor = null;

    public function mount(): void
    {
        $this->loadGrades();

        $this->majors = Major::query()->pluck('name', 'id');
    }

    public function updatedSearch(): void
    {
        $this->loadGrades();
    }

    public function updatedSelectedMajor(): void
    {
        $this->loadGrades();
    }

    protected function loadGrades(): void
    {
        $today = now()->toDateString();

        $this->grades = Grade::with(['major'])
            ->withExists([
                'attendances as has_attendance_today' => fn($query) =>
                    $query->whereDate('date', $today)
            ])
            ->when(
                filled($this->search),
                fn($query) => $query->where('name', 'like', '%' . $this->search . '%')
            )
            ->when(
                $this->selectedMajor,
                fn($query) => $query->where('major_id', $this->selectedMajor)
            )
            ->orderBy('name')
            ->get();


    }



}
