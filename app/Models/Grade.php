<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'name',
        'major_id',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    /**
     * Example: A grade has many students (if you link later).
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }


}
