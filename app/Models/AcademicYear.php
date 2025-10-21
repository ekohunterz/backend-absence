<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = [
        'start_year',
        'end_year',
        'semester',
        'is_active',
    ];


    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
