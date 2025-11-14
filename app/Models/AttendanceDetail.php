<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceDetail extends Model
{
    protected $fillable = [
        'attendance_id',
        'student_id',
        'status',
        'check_in_time',
        'check_out_time',
        'location_in',
        'location_out',
        'photo_in',
        'photo_out',
        'leave_request_id',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    /**
     * Check if has attachments
     */
    public function hasAttachments(): bool
    {
        return !empty($this->photo_in) ||
            !empty($this->photo_out) ||
            !empty($this->leave_request_id);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'hadir' => 'Hadir',
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'alpa' => 'Alpa',
            default => '-',
        };
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'hadir' => 'success',
            'sakit' => 'info',
            'izin' => 'warning',
            'alpa' => 'danger',
            default => 'gray',
        };
    }
}
