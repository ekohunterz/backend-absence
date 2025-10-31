<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Student extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'email',
        'avatar_url',
        'gender',
        'birth_date',
        'address',
        'phone',
        'parent_name',
        'parent_phone',
        'password',
        'remember_token',
        'status',
        'grade_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function attendanceDetails()
    {
        return $this->hasMany(AttendanceDetail::class);
    }

    public function is_present()
    {
        return $this->attendanceDetails()
            ->whereHas('attendance', function ($q) {
                $q->whereDate('presence_date', now()->toDateString());
            })
            ->exists();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * getJWTCustomClaims
     *
     * @return void
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
