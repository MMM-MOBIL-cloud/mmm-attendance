<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleSwapRequest extends Model
{
    protected $fillable = [
    'requester_id',
    'target_user_id',
    'from_date',
    'to_date',
    'type',
    'status'
];

    public function requester()
    {
        return $this->belongsTo(User::class,'requester_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class,'target_user_id');
    }
}
