<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = [
        'student_id',
        'grade_id',
        'academic_year_id',
        'semester_id',
        'type',
        'reason',
        'start_date',
        'end_date',
        'proof_file',
        'status',
        'response_note',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the attendance
     */
    public function attendance_detail(): BelongsTo
    {
        return $this->belongsTo(AttendanceDetail::class);
    }

    /**
     * Get the grade
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    /**
     * Get the academic year
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the semester
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Get the user who responded
     */
    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            default => 'Unknown',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Unknown',
        };
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get days count
     */
    public function getDaysCountAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Check if editable
     */
    public function isEditable(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if can be cancelled
     */
    public function isCancellable(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for current academic year
     */
    public function scopeCurrentAcademicYear($query)
    {
        $academicYear = AcademicYear::where('is_active', true)->first();

        if ($academicYear) {
            return $query->where('academic_year_id', $academicYear->id);
        }

        return $query;
    }
}
