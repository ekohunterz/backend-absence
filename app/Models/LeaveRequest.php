<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'student_id',
        'grade_id',
        'academic_year_id',
        'date',
        'type',
        'reason',
        'proof_file',
        'status',
        'verified_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function verifier()
    {
        return $this->belongsTo(Teacher::class, 'verified_by');
    }
}
