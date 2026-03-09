<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralLeave extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'reason',
        'proof',
        'status'
    ];

    public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}
}
