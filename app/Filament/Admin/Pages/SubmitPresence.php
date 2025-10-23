<?php

namespace App\Filament\Admin\Pages;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Student;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\Request;
use App\Filament\Admin\Pages\Presence;
use Illuminate\Support\Facades\DB;

class SubmitPresence extends Page
{

    protected string $view = 'filament.admin.pages.submit-presence';
    protected static ?string $title = 'Presensi';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $parent = Presence::class;
    protected static ?string $activeNavigationParent = Presence::class;
    public Grade $grade;
    public $students = [];

    public $verified = [];


    public function mount(Request $request): void
    {

        $grade = Grade::findOrFail($request->grade);

        $this->grade = $grade;

        #check if already submitted
        $alreadySubmitted = Attendance::where('grade_id', $grade->id)->whereDate('date', now()->toDateString())->first();

        if ($alreadySubmitted) {
            $this->students = $alreadySubmitted->details()->get()->map(fn($d) => [
                'id' => $d->student->id,
                'name' => $d->student->name,
                'nis' => $d->student->nis,
                'gender' => $d->student->gender,
                'status' => $d->status,
            ]);
            $this->verified = $alreadySubmitted;
        } else {
            $this->students = Student::where('grade_id', $grade->id)
                ->orderBy('name')
                ->get()
                ->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'nis' => $s->nis,
                    'gender' => $s->gender,
                    'status' => 'hadir',
                ])
                ->toArray();
        }


    }

    public function save(): void
    {
        DB::beginTransaction();

        try {
            // cari apakah absensi untuk kelas & tanggal ini sudah ada
            $attendance = Attendance::firstOrCreate(
                [
                    'grade_id' => $this->grade->id,
                    'date' => now()->toDateString(),
                ],
                [ // hanya akan dieksekusi jika belum ada
                    'start_time' => now()->toTimeString(),
                    'end_time' => now()->addHours(8)->toTimeString(),
                    'verified_by' => auth()->id(),
                    'academic_year_id' => AcademicYear::where('is_active', true)->first()->id,
                ]
            );

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
