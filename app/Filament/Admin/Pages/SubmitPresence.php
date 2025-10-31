<?php

namespace App\Filament\Admin\Pages;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Student;
use App\Services\WhatsAppService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Http\Request;
use App\Filament\Admin\Pages\Presence;
use Illuminate\Support\Facades\DB;

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



    public function mount(Request $request): void
    {
        $this->grades = Grade::orderBy('name')->get();

        $this->grade_id = $request->grade
            ? (int) $request->grade
            : ($this->grades->first()->id ?? null);

        $this->presence_date = now()->format('Y-m-d');

        $this->loadPresenceData();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            // 🔹 Pilih Kelas
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

            // 🔹 Pilih Tanggal
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
            return;
        }

        $this->grade = Grade::find($this->grade_id);

        $attendance = Attendance::with(['details.student', 'verifier'])
            ->where('grade_id', $this->grade_id)
            ->whereDate('presence_date', $this->presence_date)
            ->first();

        // Ambil semua siswa di kelas
        $students = Student::where('grade_id', $this->grade_id)
            ->orderBy('name')
            ->get();

        if ($attendance) {
            $this->verified = $attendance->verifier;
            $attendanceMap = $attendance->details->pluck('status', 'student_id')->toArray();

            $this->students = $students->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'nis' => $s->nis,
                'gender' => $s->gender,
                'status' => $attendanceMap[$s->id] ?? 'hadir',
            ])->toArray();
        } else {
            $this->verified = null;
            $this->students = $students->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'nis' => $s->nis,
                'gender' => $s->gender,
                'status' => 'hadir',
            ])->toArray();
        }
    }


    public function save(): void
    {
        DB::beginTransaction();

        try {
            // cari apakah absensi untuk kelas & tanggal ini sudah ada
            $attendance = Attendance::updateOrCreate(
                [
                    'grade_id' => $this->grade_id,
                    'presence_date' => $this->presence_date,
                ],
                [ // hanya akan dieksekusi jika belum ada
                    'start_time' => now()->toTimeString(),
                    'end_time' => now()->addHours(8)->toTimeString(),
                    'verified_by' => auth()->id(),
                    'academic_year_id' => AcademicYear::where('is_active', true)->first()->id,
                ]
            );

            // Simpan detail & kirim notifikasi
            $whatsappService = new WhatsAppService();

            // update detail tiap siswa
            foreach ($this->students as $student) {
                $attendance->details()->updateOrCreate(
                    [
                        'student_id' => $student['id'],
                    ],
                    [
                        'status' => $student['status'],
                        'check_in_time' => $student['status'] == 'hadir' ? now()->toTimeString() : null,
                    ]
                );

                // 🔔 Kirim notifikasi WhatsApp
                $studentModel = Student::find($student['id']);

                if ($studentModel && $studentModel->parent_phone) {
                    dispatch(function () use ($whatsappService, $studentModel, $student) {
                        $whatsappService->sendAttendanceNotification(
                            phoneNumber: $studentModel->parent_phone,
                            studentName: $studentModel->name,
                            status: $student['status'],
                            date: now()->format('d F Y'),
                            time: now()->format('H:i'),
                            gradeName: $studentModel->grade->name
                        );
                    })->afterResponse();
                }
            }

            DB::commit();

            Notification::make()
                ->title('Absensi berhasil disimpan!')
                ->success()
                ->send();

        } catch (\Throwable $th) {
            DB::rollBack();

            Notification::make()
                ->title('Gagal menyimpan absensi!')
                ->body($th->getMessage())
                ->danger()
                ->send();
        }

        redirect()->route('filament.admin.pages.submit-presence', ['grade' => $this->grade->id]);
    }



}
