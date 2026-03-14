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

    public function getLateMinutesAttribute()
    {
    if (!$this->check_in) {
        return 0;
    }

    $checkIn = \Carbon\Carbon::parse($this->check_in);

    // batas telat kantor MMM = 08:15
    $batas = \Carbon\Carbon::createFromTime(8, 15, 0);

    if ($checkIn->lte($batas)) {
        return 0;
    }

    return $batas->diffInMinutes($checkIn);
    }
}
