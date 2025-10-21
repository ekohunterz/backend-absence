<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'school_name',
        'school_address',
        'latitude',
        'longitude',
        'radius',
        'start_time',
        'end_time',
    ];
}
