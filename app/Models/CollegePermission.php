<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollegePermission extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'reason',
        'replace_date',
        'replace_start',
        'replace_end',
        'replace_reason',
        'status'
    ];

    public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}
}
