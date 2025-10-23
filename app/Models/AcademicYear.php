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

    protected $appends = [
        'name',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            // Jika tahun ini di-set aktif, nonaktifkan semua tahun lain
            if ($model->is_active) {
                static::where('id', '!=', $model->id)
                    ->update(['is_active' => false]);
            }
        });
    }

    public function getNameAttribute(): string
    {
        return $this->start_year . '/' . $this->end_year . ' ' . $this->semester;
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
