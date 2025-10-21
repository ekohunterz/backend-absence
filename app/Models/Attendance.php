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
        return $this->belongsTo(Teacher::class, 'verified_by');
    }

    public function details()
    {
        return $this->hasMany(AttendanceDetail::class);
    }
}
