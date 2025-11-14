<?php

namespace App\Filament\Admin\Pages;

use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Pages\Page;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Student;
use App\Services\PromotionService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;


class BulkPromotion extends Page
{
    protected string $view = 'filament.admin.pages.bulk-promotion';

    protected static ?string $navigationLabel = 'Kenaikan Kelas';

    protected static ?string $title = 'Naik Kelas Massal';

    protected static string|BackedEnum|null $navigationIcon = Phosphor::UsersThree;
    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];
    public $sourceGrade = null;
    public $targetGrade = null;
    public $students = [];
    public $selectedStudentIds = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Pilih Kelas')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('source_grade_id')
                                    ->label('Kelas Asal')
                                    ->options(Grade::orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        $this->loadStudents($state);
                                    })
                                    ->helperText('Pilih kelas yang akan dinaikan'),

                                Select::make('target_grade_id')
                                    ->label('Kelas Tujuan')
                                    ->options(Grade::orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->helperText('Pilih kelas tujuan')
                                    ->disabled(fn(Get $get) => !$get('source_grade_id')),
                            ]),

                        Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->options(AcademicYear::orderByDesc('name')->pluck('name', 'id'))
                            ->default(fn() => AcademicYear::where('is_active', true)->first()?->id)
                            ->required()
                            ->helperText('Tahun ajaran untuk pencatatan'),

                        Textarea::make('reason')
                            ->label('Alasan / Keterangan')
                            ->placeholder('Contoh: Naik kelas tahun ajaran 2024/2025')
                            ->rows(3),
                    ]),
            ])
            ->statePath('data');
    }

    protected function loadStudents(?int $gradeId): void
    {
        if (!$gradeId) {
            $this->students = [];
            return;
        }

        $this->sourceGrade = Grade::find($gradeId);

        $this->students = Student::where('grade_id', $gradeId)
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get()
            ->map(fn($student) => [
                'id' => $student->id,
                'nis' => $student->nis,
                'name' => $student->name,
                'gender' => $student->gender,
                'selected' => true, // Default semua terpilih
            ])
            ->toArray();

        // Initialize all as selected
        $this->selectedStudentIds = collect($this->students)->pluck('id')->toArray();
    }

    public function toggleStudent(int $studentId): void
    {
        if (in_array($studentId, $this->selectedStudentIds)) {
            $this->selectedStudentIds = array_diff($this->selectedStudentIds, [$studentId]);
        } else {
            $this->selectedStudentIds[] = $studentId;
        }
    }

    public function selectAll(): void
    {
        $this->selectedStudentIds = collect($this->students)->pluck('id')->toArray();
    }

    public function deselectAll(): void
    {
        $this->selectedStudentIds = [];
    }

    public function promote(): void
    {
        $this->validate([
            'data.source_grade_id' => 'required',
            'data.target_grade_id' => 'required|different:data.source_grade_id',
            'data.academic_year_id' => 'required',
        ]);

        if (empty($this->selectedStudentIds)) {
            Notification::make()
                ->title('Tidak Ada Siswa Terpilih')
                ->body('Pilih minimal 1 siswa untuk dinaikan kelas')
                ->warning()
                ->send();
            return;
        }

        $selectedStudents = Student::whereIn('id', $this->selectedStudentIds)->get();

        $service = new PromotionService();

        $result = $service->promoteMultipleStudents(
            $selectedStudents,
            $this->data['target_grade_id'],
            $this->data['reason'] ?? null,
            $this->data['academic_year_id']
        );

        if ($result['success']) {
            $message = "{$result['success_count']} siswa berhasil dinaikan ke {$result['new_grade']->name}";

            if ($result['failed_count'] > 0) {
                $message .= "\n{$result['failed_count']} siswa gagal diproses";
            }

            Notification::make()
                ->title('Proses Selesai!')
                ->body($message)
                ->success()
                ->send();

            // Reload students
            $this->loadStudents($this->data['source_grade_id']);
        } else {
            Notification::make()
                ->title('Gagal')
                ->body($result['message'])
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('promote_auto')
                ->label('Auto Promote')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Auto Promote Kelas')
                ->modalDescription('Otomatis naikan semua siswa ke kelas berikutnya berdasarkan urutan')
                ->disabled(fn() => empty($this->data['source_grade_id']))
                ->action(function () {
                    $service = new PromotionService();
                    $suggestedGrade = $service->getSuggestedNextGrade(
                        Student::where('grade_id', $this->data['source_grade_id'])->first()
                    );

                    if (!$suggestedGrade) {
                        Notification::make()
                            ->title('Tidak Ada Kelas Berikutnya')
                            ->body('Tidak ditemukan kelas lanjutan untuk kelas ini')
                            ->warning()
                            ->send();
                        return;
                    }

                    $this->data['target_grade_id'] = $suggestedGrade->id;
                    $this->promote();
                }),
        ];
    }

    public function getSelectedCount(): int
    {
        return count($this->selectedStudentIds);
    }

    public function getTotalCount(): int
    {
        return count($this->students);
    }
}
