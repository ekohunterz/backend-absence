<?php

namespace App\Services;

use App\Jobs\SendAttendanceWhatsAppJob;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Semester;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Save or update attendance data
     *
     * @param int $gradeId
     * @param string $presenceDate
     * @param array $students
     * @param int|null $verifiedBy
     * @return array
     */
    public function saveAttendance(
        int $gradeId,
        string $presenceDate,
        array $students,
        ?int $verifiedBy = null
    ): array {
        DB::beginTransaction();

        try {
            // Get active semester and academic year
            $activeSemester = Semester::where('is_active', true)->first();
            $activeAcademicYear = AcademicYear::where('is_active', true)->first();

            if (!$activeSemester || !$activeAcademicYear) {
                throw new \Exception('Semester atau Tahun Ajaran aktif tidak ditemukan');
            }

            // Create or update attendance record
            $attendance = Attendance::updateOrCreate(
                [
                    'grade_id' => $gradeId,
                    'presence_date' => $presenceDate,
                ],
                [
                    'verified_at' => now(),
                    'verified_by' => $verifiedBy ?? auth()->id(),
                    'semester_id' => $activeSemester->id,
                    'academic_year_id' => $activeAcademicYear->id,
                ]
            );

            // Save attendance details and queue WhatsApp notifications
            $notificationCount = $this->saveAttendanceDetails(
                $attendance,
                $students,
                $presenceDate
            );

            DB::commit();

            return [
                'success' => true,
                'message' => 'Absensi berhasil disimpan',
                'attendance_id' => $attendance->id,
                'notifications_queued' => $notificationCount,
            ];

        } catch (\Throwable $th) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Gagal menyimpan absensi: ' . $th->getMessage(),
                'error' => $th,
            ];
        }
    }

    /**
     * Save attendance details for each student
     *
     * @param Attendance $attendance
     * @param array $students
     * @param string $presenceDate
     * @return int
     */
    protected function saveAttendanceDetails(
        Attendance $attendance,
        array $students,
        string $presenceDate
    ): int {
        $notificationCount = 0;
        $date = Carbon::parse($presenceDate)->format('d F Y');
        $time = now()->format('H:i');

        foreach ($students as $student) {

            $checkInTime = $student['status'] === 'hadir'
                ? ($student['check_in_time'] ?? now()->format('H:i'))
                : null;


            // Update or create attendance detail
            $attendance->details()->updateOrCreate(
                [
                    'student_id' => $student['id'],
                ],
                [
                    'status' => $student['status'],
                    'check_in_time' => $checkInTime,
                ]
            );

            // Queue WhatsApp notification
            $studentModel = Student::with('grade')->find($student['id']);

            if ($studentModel && $studentModel->parent_phone) {
                $this->queueWhatsAppNotification(
                    $studentModel,
                    $student['status'],
                    $date,
                    $time,
                    $notificationCount
                );

                $notificationCount++;
            }
        }

        return $notificationCount;
    }

    /**
     * Queue WhatsApp notification for attendance
     *
     * @param Student $student
     * @param string $status
     * @param string $date
     * @param string $time
     * @param int $delayMultiplier
     * @return void
     */
    protected function queueWhatsAppNotification(
        Student $student,
        string $status,
        string $date,
        string $time,
        int $delayMultiplier
    ): void {
        SendAttendanceWhatsAppJob::dispatch(
            student: $student,
            status: $status,
            date: $date,
            time: $time,
            gradeName: $student->grade->name
        )
            ->onQueue('whatsapp')
            ->delay(now()->addSeconds($delayMultiplier * 2)); // Delay 2 detik per notifikasi
    }

    /**
     * Get attendance data for specific grade and date
     *
     * @param int $gradeId
     * @param string $presenceDate
     * @return array
     */
    public function getAttendanceData(int $gradeId, string $presenceDate): array
    {
        $attendance = Attendance::with(['details.student', 'verifier'])
            ->where('grade_id', $gradeId)
            ->whereDate('presence_date', $presenceDate)
            ->first();

        $students = Student::where('grade_id', $gradeId)
            ->where('status', 'aktif')
            ->orderBy('name')
            ->get();

        if ($attendance) {
            $verified = [
                'name' => $attendance->verifier?->name,
                'at' => Carbon::parse($attendance->verified_at)->diffForHumans(),
                'full_date' => Carbon::parse($attendance->verified_at)->format('d M Y H:i'),
            ];

            $attendanceDetails = $attendance->details->keyBy('student_id');

            $studentsData = $students->map(function ($s) use ($attendanceDetails) {
                $detail = $attendanceDetails->get($s->id);

                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'nis' => $s->nis,
                    'gender' => $s->gender,
                    'status' => $detail->status ?? 'hadir',
                    'check_in_time' => $detail?->check_in_time,
                    'check_out_time' => $detail?->check_out_time,
                    'photo_in' => $detail?->photo_in,
                    'photo_out' => $detail?->photo_out,
                    'permission_proof' => $detail?->leaveRequest?->proof_file,
                    'location_in' => $detail?->location_in,
                    'location_out' => $detail?->location_out,
                    'notes' => $detail?->leaveRequest?->reason,
                ];
            })->toArray();
        } else {
            $verified = null;

            $studentsData = $students->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'nis' => $s->nis,
                'gender' => $s->gender,
                'status' => 'hadir',
                'check_in_time' => null,
                'check_out_time' => null,
                'photo_in' => null,
                'photo_out' => null,
                'permission_proof' => null,
                'location_in' => null,
                'location_out' => null,
                'notes' => null,
            ])->toArray();
        }

        return [
            'students' => $studentsData,
            'verified' => $verified,
            'attendance' => $attendance,
        ];
    }

    /**
     * Bulk import attendance from array
     *
     * @param int $gradeId
     * @param string $presenceDate
     * @param array $attendanceData
     * @param int|null $verifiedBy
     * @return array
     */
    public function bulkImportAttendance(
        int $gradeId,
        string $presenceDate,
        array $attendanceData,
        ?int $verifiedBy = null
    ): array {
        $students = [];
        $errors = [];

        foreach ($attendanceData as $index => $data) {
            // Validate student exists
            $student = Student::where('nis', $data['nis'])
                ->where('grade_id', $gradeId)
                ->first();

            if (!$student) {
                $errors[] = "Baris {$index}: Siswa dengan NIS {$data['nis']} tidak ditemukan";
                continue;
            }

            // Validate status
            if (!in_array($data['status'], ['hadir', 'sakit', 'izin', 'alpa'])) {
                $errors[] = "Baris {$index}: Status tidak valid untuk {$student->name}";
                continue;
            }

            $students[] = [
                'id' => $student->id,
                'status' => $data['status'],
            ];
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Terdapat error pada data import',
                'errors' => $errors,
            ];
        }

        return $this->saveAttendance($gradeId, $presenceDate, $students, $verifiedBy);
    }

    /**
     * Get attendance statistics for a grade in a date range
     *
     * @param int $gradeId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getAttendanceStatistics(
        int $gradeId,
        string $startDate,
        string $endDate
    ): array {
        $attendances = Attendance::where('grade_id', $gradeId)
            ->whereBetween('presence_date', [$startDate, $endDate])
            ->with('details')
            ->get();

        $statistics = [
            'total_days' => $attendances->count(),
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
        ];

        foreach ($attendances as $attendance) {
            foreach ($attendance->details as $detail) {
                if (isset($statistics[$detail->status])) {
                    $statistics[$detail->status]++;
                }
            }
        }

        return $statistics;
    }
}