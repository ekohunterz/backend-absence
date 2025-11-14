<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\GradeHistory;
use App\Models\Student;
use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PromotionService
{
    /**
     * Promote single student to new grade
     */
    public function promoteSingleStudent(
        Student $student,
        int $newGradeId,
        ?string $reason = null,
        ?int $academicYearId = null
    ): array {
        DB::beginTransaction();

        try {
            $oldGrade = $student->grade;
            $newGrade = Grade::findOrFail($newGradeId);

            // Prevent promoting to same grade
            if ($oldGrade->id === $newGrade->id) {
                throw new \Exception('Siswa sudah berada di kelas tersebut');
            }

            // Save history
            $this->saveGradeHistory($student, $oldGrade, $newGrade, $reason, $academicYearId);

            // Update student grade
            $student->update([
                'grade_id' => $newGradeId,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => "Siswa {$student->name} berhasil dipindahkan ke {$newGrade->name}",
                'student' => $student,
                'old_grade' => $oldGrade,
                'new_grade' => $newGrade,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e,
            ];
        }
    }

    /**
     * Promote multiple students to new grade
     */
    public function promoteMultipleStudents(
        Collection $students,
        int $newGradeId,
        ?string $reason = null,
        ?int $academicYearId = null
    ): array {
        DB::beginTransaction();

        try {
            $newGrade = Grade::findOrFail($newGradeId);
            $successCount = 0;
            $failedStudents = [];
            $oldGrades = [];

            foreach ($students as $student) {
                try {
                    $oldGrade = $student->grade;

                    // Skip if already in target grade
                    if ($oldGrade->id === $newGrade->id) {
                        $failedStudents[] = [
                            'name' => $student->name,
                            'reason' => 'Sudah berada di kelas tersebut'
                        ];
                        continue;
                    }

                    // Save history
                    $this->saveGradeHistory($student, $oldGrade, $newGrade, $reason, $academicYearId);

                    // Update student grade
                    $student->update([
                        'grade_id' => $newGradeId,
                    ]);

                    // Track old grades for summary
                    $oldGradeName = $oldGrade->name;
                    if (!isset($oldGrades[$oldGradeName])) {
                        $oldGrades[$oldGradeName] = 0;
                    }
                    $oldGrades[$oldGradeName]++;

                    $successCount++;

                } catch (\Exception $e) {
                    $failedStudents[] = [
                        'name' => $student->name,
                        'reason' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Berhasil memindahkan {$successCount} siswa ke {$newGrade->name}",
                'success_count' => $successCount,
                'failed_count' => count($failedStudents),
                'failed_students' => $failedStudents,
                'old_grades' => $oldGrades,
                'new_grade' => $newGrade,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e,
            ];
        }
    }

    /**
     * Auto promote entire grade to next level
     */
    public function autoPromoteGrade(
        int $sourceGradeId,
        int $targetGradeId,
        ?int $academicYearId = null
    ): array {
        $sourceGrade = Grade::with('students')->findOrFail($sourceGradeId);
        $targetGrade = Grade::findOrFail($targetGradeId);

        $students = $sourceGrade->students()
            ->where('status', 'aktif')
            ->get();

        if ($students->isEmpty()) {
            return [
                'success' => false,
                'message' => "Tidak ada siswa aktif di kelas {$sourceGrade->name}",
            ];
        }

        $reason = "Naik kelas otomatis dari {$sourceGrade->name} ke {$targetGrade->name}";

        return $this->promoteMultipleStudents(
            $students,
            $targetGradeId,
            $reason,
            $academicYearId
        );
    }

    /**
     * Graduate students (promote to alumni)
     */
    public function graduateStudents(
        Collection $students,
        ?int $academicYearId = null
    ): array {
        DB::beginTransaction();

        try {
            $successCount = 0;
            $failedStudents = [];

            foreach ($students as $student) {
                try {
                    $oldGrade = $student->grade;

                    // Save history
                    $this->saveGradeHistory(
                        $student,
                        $oldGrade,
                        null,
                        'Lulus / Alumni',
                        $academicYearId
                    );

                    // Update student status
                    $student->update([
                        'status' => 'alumni',
                    ]);

                    $successCount++;

                } catch (\Exception $e) {
                    $failedStudents[] = [
                        'name' => $student->name,
                        'reason' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Berhasil meluluskan {$successCount} siswa",
                'success_count' => $successCount,
                'failed_count' => count($failedStudents),
                'failed_students' => $failedStudents,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e,
            ];
        }
    }

    /**
     * Save grade change history
     */
    protected function saveGradeHistory(
        Student $student,
        ?Grade $oldGrade,
        ?Grade $newGrade,
        ?string $reason,
        ?int $academicYearId
    ): void {
        GradeHistory::create([
            'student_id' => $student->id,
            'old_grade_id' => $oldGrade?->id,
            'new_grade_id' => $newGrade?->id,
            'academic_year_id' => $academicYearId ?? AcademicYear::where('is_active', true)->first()?->id,
            'promotion_date' => now(),
            'reason' => $reason,
        ]);
    }

    /**
     * Get promotion statistics
     */
    public function getPromotionStats(int $academicYearId): array
    {
        $histories = GradeHistory::where('academic_year_id', $academicYearId)
            ->with(['student', 'oldGrade', 'newGrade'])
            ->get();

        $totalPromoted = $histories->count();
        $graduated = $histories->whereNull('new_grade_id')->count();
        $promoted = $totalPromoted - $graduated;

        $promotionsByGrade = $histories
            ->whereNotNull('new_grade_id')
            ->groupBy('new_grade_id')
            ->map(fn($group) => $group->count());

        return [
            'total_promoted' => $totalPromoted,
            'promoted_to_next_grade' => $promoted,
            'graduated' => $graduated,
            'promotions_by_grade' => $promotionsByGrade,
        ];
    }

    /**
     * Get student promotion history
     */
    public function getStudentHistory(int $studentId): Collection
    {
        return GradeHistory::where('student_id', $studentId)
            ->with(['oldGrade', 'newGrade', 'academicYear'])
            ->orderBy('promotion_date', 'desc')
            ->get();
    }

    /**
     * Validate promotion (check prerequisites)
     */
    public function validatePromotion(Student $student, int $newGradeId): array
    {
        $errors = [];
        $warnings = [];

        $newGrade = Grade::find($newGradeId);

        if (!$newGrade) {
            $errors[] = 'Kelas tujuan tidak ditemukan';
            return ['valid' => false, 'errors' => $errors, 'warnings' => $warnings];
        }

        // Check if student is active
        if ($student->status !== 'aktif') {
            $errors[] = 'Siswa tidak aktif';
        }

        // Check if same grade
        if ($student->grade_id === $newGradeId) {
            $errors[] = 'Siswa sudah berada di kelas tersebut';
        }

        // Warning if grade capacity exceeded
        $currentCapacity = Student::where('grade_id', $newGradeId)
            ->where('status', 'aktif')
            ->count();

        if ($newGrade->capacity && $currentCapacity >= $newGrade->capacity) {
            $warnings[] = "Kelas {$newGrade->name} sudah penuh (kapasitas: {$newGrade->capacity})";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Get suggested next grade
     */
    public function getSuggestedNextGrade(Student $student): ?Grade
    {
        $currentGrade = $student->grade;

        if (!$currentGrade) {
            return null;
        }

        // Ambil nama kelas, misal: "X TKJ 2"
        $name = strtoupper($currentGrade->name);

        // Deteksi jenjang saat ini (X, XI, XII)
        if (str_contains($name, 'XII')) {
            // Sudah kelas terakhir
            return null;
        }

        // Tentukan next level
        if (str_contains($name, 'XI')) {
            $nextName = str_replace('XI', 'XII', $name);
        } elseif (str_contains($name, 'X ')) { // ada spasi untuk hindari "XII"
            $nextName = str_replace('X ', 'XI ', $name);
        } else {
            $nextName = null;
        }

        if (!$nextName) {
            return null;
        }

        // Cari grade dengan nama hasil penggantian
        return Grade::whereRaw('UPPER(name) = ?', [$nextName])->first();
    }

}