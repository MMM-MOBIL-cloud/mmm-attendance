<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'type',
        'color',
        'created_by'
    ];
}
