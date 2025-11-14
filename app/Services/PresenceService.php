<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\LeaveRequest;
use App\Models\Semester;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PresenceService
{



    public function getTodayPresence($student)
    {
        $todayPresence = AttendanceDetail::whereHas('attendance', function ($query) use ($student) {
            $query->where('grade_id', $student->grade_id)
                ->whereDate('presence_date', today());
        })
            ->where('student_id', $student->id)
            ->first();

        return $todayPresence;
    }

    public function getPermissionStatus($student)
    {
        $leaveRequest = LeaveRequest::where('student_id', $student->id)
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->where('status', '!=', 'rejected')
            ->first();

        return $leaveRequest;
    }

    /**
     * Check if location is within radius
     */
    public function validateLocation($latitude, $longitude): bool
    {
        $setting = Setting::first();

        $schoolLat = $setting->latitude;
        $schoolLng = $setting->longitude;
        $radius = $setting->radius;

        $distance = $this->calculateDistance($schoolLat, $schoolLng, $latitude, $longitude);

        return $distance <= $radius;
    }

    /**
     * Calculate distance between two coordinates (Haversine formula)
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // meters

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Save photo to storage
     */
    public function savePhoto($photoBase64, $type, $studentId): string
    {
        // Remove base64 prefix
        $photo = str_replace('data:image/jpeg;base64,', '', $photoBase64);
        $photo = str_replace(' ', '+', $photo);
        $photoData = base64_decode($photo);

        // Generate filename
        $filename = sprintf(
            '%s_%s_%s.jpg',
            $studentId,
            $type,
            now()->format('YmdHis')
        );

        // Save to storage
        $path = "attendance/photos/{$type}/" . now()->format('Y/m');
        Storage::disk('public')->put("{$path}/{$filename}", $photoData);

        return "{$path}/{$filename}";
    }

    /**
     * Get or create today's attendance record
     */
    public function getOrCreateAttendance($student)
    {
        return Attendance::firstOrCreate(
            [
                'grade_id' => $student->grade_id,
                'presence_date' => today(),
            ],
            [
                'semester_id' => Semester::where('is_active', true)->first()->id,
                'academic_year_id' => AcademicYear::where('is_active', true)->first()->id,
            ]
        );
    }



}