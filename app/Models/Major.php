<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * One major has many classes.
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
