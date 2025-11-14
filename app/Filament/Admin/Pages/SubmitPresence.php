<?php

namespace App\Filament\Admin\Pages;

use App\Models\Grade;
use App\Models\Student;
use App\Services\AttendanceService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Http\Request;
use App\Filament\Admin\Pages\Presence;

class SubmitPresence extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.admin.pages.submit-presence';
    protected static ?string $title = 'Presensi';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $parent = Presence::class;
    protected static ?string $activeNavigationParent = Presence::class;

    public ?Grade $grade = null;
    public $grades = [];
    public $students = [];
    public ?string $presence_date = null;
    public $verified = null;
    public ?int $grade_id = null;
    public $selectedStudent = null;
    public $showDetailModal = false;

    protected AttendanceService $attendanceService;

    public function boot(AttendanceService $attendanceService): void
    {
        $this->attendanceService = $attendanceService;
    }

    public function mount(Request $request): void
    {
        $this->grades = Grade::orderBy('name')->get();

        $this->grade_id = $request->grade
            ? (int) $request->grade
            : ($this->grades->first()->id ?? null);

        $this->presence_date = $request->date ?? now()->format('Y-m-d');

        $this->loadPresenceData();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('grade_id')
                ->hiddenLabel()
                ->placeholder('Pilih Kelas')
                ->options($this->grades->pluck('name', 'id')->toArray())
                ->searchable()
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    $this->grade_id = $state;
                    $this->loadPresenceData();
                }),

            DatePicker::make('presence_date')
                ->hiddenLabel()
                ->placeholder('Tanggal Presensi')
                ->default($this->presence_date)
                ->maxDate(now()->format('Y-m-d'))
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    $this->presence_date = $state ?: now()->format('Y-m-d');
                    $this->loadPresenceData();
                }),
        ])->columns(2);
    }

    protected function loadPresenceData(): void
    {
        if (!$this->grade_id) {
            $this->students = [];
            $this->verified = null;
            $this->grade = null;
            return;
        }

        $this->grade = Grade::find($this->grade_id);

        $data = $this->attendanceService->getAttendanceData(
            $this->grade_id,
            $this->presence_date
        );

        $this->students = $data['students'];
        $this->verified = $data['verified'];
    }

    public function save()
    {
        // Validate
        if (!$this->grade_id || !$this->presence_date || empty($this->students)) {
            Notification::make()
                ->title('Data tidak lengkap')
                ->body('Pastikan kelas, tanggal, dan data siswa sudah terisi.')
                ->warning()
                ->send();
            return;
        }

        // Save attendance using service
        $result = $this->attendanceService->saveAttendance(
            gradeId: $this->grade_id,
            presenceDate: $this->presence_date,
            students: $this->students,
            verifiedBy: auth()->id()
        );

        if ($result['success']) {
            Notification::make()
                ->title('Berhasil!')
                ->body($result['message'] . ($result['notifications_queued'] > 0
                    ? " ({$result['notifications_queued']} notifikasi WhatsApp dijadwalkan)"
                    : ''))
                ->success()
                ->send();

            // Reload data to show verification status
            $this->loadPresenceData();
        } else {
            Notification::make()
                ->title('Gagal menyimpan absensi')
                ->body($result['message'])
                ->danger()
                ->send();
        }
    }

    public function setAllStatus(string $status): void
    {
        if (!in_array($status, ['hadir', 'sakit', 'izin', 'alpa'])) {
            return;
        }

        foreach ($this->students as $index => $student) {
            $this->students[$index]['status'] = $status;
        }

        Notification::make()
            ->title('Status diubah')
            ->body("Semua siswa diset sebagai: " . ucfirst($status))
            ->info()
            ->send();
    }

    public function getStudentCount(): int
    {
        return count($this->students);
    }

    public function getStatusCounts(): array
    {
        $counts = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
        ];

        foreach ($this->students as $student) {
            if (isset($counts[$student['status']])) {
                $counts[$student['status']]++;
            }
        }

        return $counts;
    }

    public function showStudentDetail(int $index): void
    {
        $this->selectedStudent = $this->students[$index] ?? null;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedStudent = null;
    }

    public function hasAttachments(?array $student): bool
    {
        if (!$student)
            return false;

        return !empty($student['photo_in']) ||
            !empty($student['photo_out']) ||
            !empty($student['permission_proof']);
    }
}