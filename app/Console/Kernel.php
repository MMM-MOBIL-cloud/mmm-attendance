<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Auto checkout setiap hari jam 21:00
        $schedule->command('attendance:auto-checkout')->dailyAt('21:00');

        // Hapus foto absensi bulan sebelumnya setiap tanggal 1 jam 01:00
    $schedule->command('attendance:delete-old-photos')->monthlyOn(1, '01:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
