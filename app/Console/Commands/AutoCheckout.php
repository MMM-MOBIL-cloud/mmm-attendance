<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use Carbon\Carbon;

class AutoCheckout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto checkout jika user lupa absen pulang';

    /**
     * Execute the console command.
     */
    public function handle()
{
    $today = Carbon::today()->toDateString();

    $attendances = Attendance::whereDate('date', $today)
        ->whereNotNull('check_in')
        ->whereNull('check_out')
        ->get();

    foreach ($attendances as $attendance) {

        $attendance->check_out = Carbon::parse($today.' 16:00:00');

$attendance->checkout_type = 'auto';

$attendance->checkout_approval_status = 'Pending';

        $attendance->save();
    }

    $this->info('Auto checkout berhasil dijalankan.');
}
}
