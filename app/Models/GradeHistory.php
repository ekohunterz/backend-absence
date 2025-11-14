<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'old_grade_id',
        'new_grade_id',
        'academic_year_id',
        'promotion_date',
        'reason',
        'notes',
    ];

    protected $casts = [
        'promotion_date' => 'date',
    ];

    /**
     * Get the student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the old grade
     */
    public function oldGrade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'old_grade_id');
    }

    /**
     * Get the new grade
     */
    public function newGrade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'new_grade_id');
    }

    /**
     * Get the academic year
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get promotion type label
     */
    public function getPromotionTypeAttribute(): string
    {
        if ($this->old_grade_id && $this->new_grade_id) {
            return 'Naik Kelas';
        } elseif ($this->old_grade_id && !$this->new_grade_id) {
            return 'Lulus / Alumni';
        } elseif (!$this->old_grade_id && $this->new_grade_id) {
            return 'Siswa Baru';
        }

        return 'Unknown';
    }

    /**
     * Get promotion summary
     */
    public function getSummaryAttribute(): string
    {
        $from = $this->oldGrade?->name ?? 'N/A';
        $to = $this->newGrade?->name ?? 'Alumni';

        return "{$from} → {$to}";
    }
}