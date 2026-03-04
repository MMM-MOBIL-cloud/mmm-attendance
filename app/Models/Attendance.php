<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'approval_status',
        'checkout_approval_status',
        'photo',
        'latitude',
        'longitude',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function calculateDistance($lat, $lng)
    {
        $officeLat = config('app.office_location.latitude');
        $officeLng = config('app.office_location.longitude');

        $earthRadius = 6371000;

        $dLat = deg2rad($officeLat - $lat);
        $dLng = deg2rad($officeLng - $lng);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat)) *
            cos(deg2rad($officeLat)) *
            sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    public function getDistanceAttribute()
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        return round(
            $this->calculateDistance($this->latitude, $this->longitude)
        );
    }
}
