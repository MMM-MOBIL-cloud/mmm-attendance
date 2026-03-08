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
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
