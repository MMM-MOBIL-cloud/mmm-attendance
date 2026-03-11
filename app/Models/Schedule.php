<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal',
        'shift'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
