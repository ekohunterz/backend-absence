<?php

namespace App\Filament\Student\Pages;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\Setting;
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

    public $presenceToday;
    public Setting $setting;

    public function mount(): void
    {
        $this->presenceToday = $this->getTodayPresence();
        $this->setting = Setting::first();
    }

    protected function getTodayPresence()
    {
        $student = auth('student')->user();

        if (!$student) {
            return null;
        }

        // Get today's attendance for student's grade
        $attendance = Attendance::where('grade_id', $student->grade_id)
            ->whereDate('presence_date', today())
            ->first();

        if (!$attendance) {
            return null;
        }

        // Get student's attendance detail
        return AttendanceDetail::where('attendance_id', $attendance->id)
            ->where('student_id', $student->id)
            ->first();
    }

    public function checkIn(float $latitude, float $longitude, string $photoBase64): void
    {
        try {
            $student = auth('student')->user();

            if (!$student) {
                throw new \Exception('Siswa tidak ditemukan');
            }

            // Check if already checked in today
            if ($this->presenceToday) {
                Notification::make()
                    ->title('Sudah Absen')
                    ->body('Anda sudah melakukan presensi hari ini')
                    ->warning()
                    ->send();
                return;
            }

            // Validate location distance
            $distance = $this->calculateDistance(
                $this->setting->latitude,
                $this->setting->longitude,
                $latitude,
                $longitude
            );

            if ($distance > $this->setting->radius) {
                Notification::make()
                    ->title('Gagal Check In')
                    ->body("Anda terlalu jauh dari sekolah ({$distance}m). Radius maksimal: {$this->setting->radius}m")
                    ->danger()
                    ->send();
                return;
            }

            DB::beginTransaction();

            // Save photo
            $photoPath = $this->savePhoto($photoBase64, $student->id);

            // Get or create today's attendance record
            $attendance = Attendance::firstOrCreate(
                [
                    'grade_id' => $student->grade_id,
                    'presence_date' => today(),
                ],
                [
                    'academic_year_id' => AcademicYear::where('is_active', true)->first()->id,
                    'verified_by' => null,
                ]
            );

            // Create attendance detail
            $attendanceDetail = AttendanceDetail::create([
                'attendance_id' => $attendance->id,
                'student_id' => $student->id,
                'status' => 'hadir',
                'check_in_time' => now()->toTimeString(),
                'location' => "{$latitude},{$longitude}",
                'photo_in' => $photoPath,
            ]);

            DB::commit();

            // Update state
            $this->presenceToday = $attendanceDetail;

            Notification::make()
                ->title('Check In Berhasil!')
                ->body('Presensi Anda telah tercatat pada ' . now()->format('H:i'))
                ->success()
                ->send();

            // Refresh page component
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Check In Gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();

            \Log::error('Check-in error: ' . $e->getMessage(), [
                'student_id' => auth('student')->id(),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Calculate distance using Haversine formula
     */
    protected function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371000; // meters

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Save base64 photo to storage
     */

    public function checkOut(float $latitude, float $longitude, string $photoBase64): void
    {
        try {
            $student = auth('student')->user();

            if (!$student) {
                throw new \Exception('Siswa tidak ditemukan');
            }

            // Check if not checked in yet
            if (!$this->presenceToday) {
                Notification::make()
                    ->title('Belum Check-In')
                    ->body('Anda harus check-in terlebih dahulu sebelum check-out')
                    ->warning()
                    ->send();
                return;
            }

            // Check if already checked out
            if ($this->presenceToday->check_out_time) {
                Notification::make()
                    ->title('Sudah Check-Out')
                    ->body('Anda sudah melakukan check-out hari ini')
                    ->warning()
                    ->send();
                return;
            }

            // Validate location distance
            $distance = $this->calculateDistance(
                $this->setting->latitude,
                $this->setting->longitude,
                $latitude,
                $longitude
            );

            if ($distance > $this->setting->radius) {
                Notification::make()
                    ->title('Gagal Check-Out')
                    ->body("Anda terlalu jauh dari sekolah ({$distance}m). Radius maksimal: {$this->setting->radius}m")
                    ->danger()
                    ->send();
                return;
            }

            DB::beginTransaction();

            // Save checkout photo
            $photoPath = $this->savePhoto($photoBase64, $student->id, 'checkout');

            // Update attendance detail with check-out info
            $this->presenceToday->update([
                'check_out_time' => now()->toTimeString(),
                'photo_out' => $photoPath,
            ]);

            DB::commit();

            // Refresh state
            $this->presenceToday = $this->presenceToday->fresh();

            // Calculate work duration
            $checkInTime = Carbon::parse($this->presenceToday->check_in_time);
            $checkOutTime = Carbon::parse($this->presenceToday->check_out_time);
            $duration = $checkInTime->diff($checkOutTime);

            Notification::make()
                ->title('Check-Out Berhasil!')
                ->body("Anda telah check-out pada {$checkOutTime->format('H:i')}. Total jam kerja: {$duration->h} jam {$duration->i} menit")
                ->success()
                ->send();

            // Refresh component
            $this->dispatch('$refresh');

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('Check-Out Gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();

            \Log::error('Check-out error: ' . $e->getMessage(), [
                'student_id' => auth('student')->id(),
                'latitude' => $latitude,
                'longitude' => $longitude,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Updated savePhoto to support checkout
     */
    protected function savePhoto(string $base64, int $studentId, string $type = 'checkin'): string
    {
        // Remove base64 prefix
        $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $image = str_replace(' ', '+', $image);

        // Decode base64
        $imageData = base64_decode($image);

        if ($imageData === false) {
            throw new \Exception('Invalid image data');
        }

        // Validate size (max 5MB)
        $imageSize = strlen($imageData);
        if ($imageSize > 5 * 1024 * 1024) {
            throw new \Exception('Ukuran foto terlalu besar (maksimal 5MB)');
        }

        // Generate filename
        $filename = "{$type}_{$studentId}_" . now()->format('YmdHis') . '.jpg';
        $path = 'attendance/photos/' . now()->format('Y/m');
        $fullPath = $path . '/' . $filename;

        // Save to storage
        Storage::disk('public')->put($fullPath, $imageData);

        return $fullPath;
    }
}
