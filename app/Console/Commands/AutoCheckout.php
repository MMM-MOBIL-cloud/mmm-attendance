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
    protected $signature = 'app:auto-checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
{
    $today = Carbon::today()->toDateString();

    $attendances = Attendance::whereDate('date', $today)
        ->whereNull('check_out')
        ->get();

    foreach ($attendances as $attendance) {

        $attendance->check_out = '16:00:00';
        $attendance->checkout_approval_status = 'Approved';

        $attendance->save();
    }

    $this->info('Auto checkout berhasil dijalankan.');
}
}
