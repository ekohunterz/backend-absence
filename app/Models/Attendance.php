<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'grade_id',
        'academic_year_id',
        'verified_by',
    ];

    protected $appends = [
        'present_count',
        'absent_count',
        'sick_count',
        'leave_count',
    ];


    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // teacher who verifies attendance
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function details()
    {
        return $this->hasMany(AttendanceDetail::class);
    }

    public function getPresentCountAttribute()
    {
        return $this->details()->where('status', 'hadir')->count();
    }

    public function getAbsentCountAttribute()
    {
        return $this->details()->where('status', 'alpa')->count();
    }

    public function getSickCountAttribute()
    {
        return $this->details()->where('status', 'sakit')->count();
    }

    public function getLeaveCountAttribute()
    {
        return $this->details()->where('status', 'izin')->count();
    }
}
