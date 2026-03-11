<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWorkDay extends Model
{
    protected $table = 'user_work_days';

    protected $fillable = [
        'user_id',
        'day'
    ];
}
