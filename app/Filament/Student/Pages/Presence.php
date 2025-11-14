<?php

namespace App\Filament\Student\Pages;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\LeaveRequest;
use App\Models\Semester;
use App\Models\Setting;
use App\Models\Student;
use App\Services\PresenceService;
use Carbon\Carbon;
use BackedEnum;
use UnitEnum;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Presence extends Page
{
    protected string $view = 'filament.student.pages.presence';
    protected static ?string $title = 'Presensi';

    protected static string|UnitEnum|null $navigationGroup = 'Fitur';

    protected static ?int $navigationSort = 2;

    protected static string|null|BackedEnum $navigationIcon = Phosphor::Fingerprint;

    public $setting;
    public $presenceToday;
    public $hasPermission = false;
    public $permissionStatus = null;
    public $permissionReason = null;
    public $permissionRecordedAt = null;

    protected PresenceService $presenceService;

    public function boot(PresenceService $presenceService): void
    {
        $this->presenceService = $presenceService;
    }

    public function mount(): void
    {
        $this->setting = Setting::first();
        $this->loadTodayPresence();
    }

    /**
     * Load today's presence data and check for permissions
     */
    protected function loadTodayPresence(): void
    {
        $student = auth('student')->user();

        if (!$student) {
            Notification::make()
                ->title('Error')
                ->body('Data siswa tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        // Get today's attendance detail
        $this->presenceToday = $this->presenceService->getTodayPresence($student);

        // Check if student has permission (izin/sakit/alpa)

        $this->checkPermissionStatus();

    }

    /**
     * Check if student has permission status that prevents check-in
     */
    protected function checkPermissionStatus(): void
    {
        $student = auth('student')->user();
        $leaveRequest = $this->presenceService->getPermissionStatus($student);

        if ($leaveRequest) {
            $this->hasPermission = true;
            $this->permissionStatus = $leaveRequest->type;
            $this->permissionReason = $leaveRequest->reason;
            $this->permissionRecordedAt = $leaveRequest->created_at;
        }
    }

    /**
     * Check In
     */
    public function checkIn($latitude, $longitude, $photo): void
    {
        // Prevent check-in if has permission
        if ($this->hasPermission) {
            Notification::make()
                ->title('Tidak Dapat Check-In')
                ->body("Anda sudah tercatat {$this->permissionStatus} hari ini")
                ->warning()
                ->send();
            return;
        }

        // Validate location
        if (!$this->presenceService->validateLocation($latitude, $longitude)) {
            Notification::make()
                ->title('Lokasi Tidak Valid')
                ->body('Anda berada di luar radius presensi')
                ->danger()
                ->send();
            return;
        }

        // Validate photo
        if (!$photo) {
            Notification::make()
                ->title('Foto Diperlukan')
                ->body('Silakan ambil foto selfie terlebih dahulu')
                ->warning()
                ->send();
            return;
        }

        try {
            $student = auth('student')->user();

            // Save photo
            $photoPath = $this->presenceService->savePhoto($photo, 'check-in', $student->id);

            // Create or update attendance detail
            $this->presenceToday = AttendanceDetail::updateOrCreate(
                [
                    'attendance_id' => $this->presenceService->getOrCreateAttendance($student)->id,
                    'student_id' => $student->id,
                ],
                [
                    'status' => 'hadir',
                    'check_in_time' => now(),
                    'location_in' => $latitude . ',' . $longitude,
                    'photo_in' => $photoPath,
                ]
            );

            Notification::make()
                ->title('Berhasil Check-In')
                ->body('Presensi masuk berhasil dicatat')
                ->success()
                ->send();

            // Reload data
            $this->loadTodayPresence();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Check-In')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Check Out
     */
    public function checkOut($latitude, $longitude, $photo): void
    {
        // Prevent check-out if has permission
        if ($this->hasPermission) {
            Notification::make()
                ->title('Tidak Dapat Check-Out')
                ->body("Anda sudah tercatat {$this->permissionStatus} hari ini")
                ->warning()
                ->send();
            return;
        }

        // Validate if already checked in
        if (!$this->presenceToday || !$this->presenceToday->check_in_time) {
            Notification::make()
                ->title('Belum Check-In')
                ->body('Anda harus check-in terlebih dahulu')
                ->warning()
                ->send();
            return;
        }

        // Validate if already checked out
        if ($this->presenceToday->check_out_time) {
            Notification::make()
                ->title('Sudah Check-Out')
                ->body('Anda sudah melakukan check-out hari ini')
                ->warning()
                ->send();
            return;
        }

        // Validate location
        if (!$this->presenceService->validateLocation($latitude, $longitude)) {
            Notification::make()
                ->title('Lokasi Tidak Valid')
                ->body('Anda berada di luar radius presensi')
                ->danger()
                ->send();
            return;
        }

        try {

            // Save photo
            $photoPath = $this->presenceService->savePhoto($photo, 'check-out', auth('student')->user()->id);

            // Update attendance detail
            $this->presenceToday->update([
                'check_out_time' => now(),
                'location_out' => $latitude . ',' . $longitude,
                'photo_out' => $photoPath,
            ]);

            Notification::make()
                ->title('Berhasil Check-Out')
                ->body('Presensi keluar berhasil dicatat')
                ->success()
                ->send();

            // Reload data
            $this->loadTodayPresence();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Check-Out')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

}
