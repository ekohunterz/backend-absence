<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Student extends Authenticatable implements JWTSubject, FilamentUser, HasAvatar
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

    public function getFilamentAvatarUrl(): string
    {
        if ($this->avatar_url) {
            return asset('storage/' . $this->avatar_url);
        }
        $hash = md5(mb_strtolower(mb_trim($this->email)));

        return 'https://www.gravatar.com/avatar/' . $hash . '?d=mp&r=g&s=250';

    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

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
