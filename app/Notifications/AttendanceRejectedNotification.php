<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AttendanceRejectedNotification extends Notification
{
    use Queueable;

    protected $attendance;

    public function __construct($attendance)
    {
        $this->attendance = $attendance;
    }

    public function via($notifiable)
    {
        return ['database']; // simpan ke database
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Absensi tanggal ' . $this->attendance->date . ' ditolak admin. Silakan lakukan absen ulang.',
            'attendance_id' => $this->attendance->id,
        ];
    }
}
