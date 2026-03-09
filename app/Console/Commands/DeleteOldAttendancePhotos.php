<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DeleteOldAttendancePhotos extends Command
{
    protected $signature = 'attendance:delete-old-photos';

    protected $description = 'Hapus folder foto absensi bulan sebelumnya';

    public function handle()
    {
        $lastMonth = Carbon::now()->subMonth()->format('Y-m');

        $folder = 'attendance/' . $lastMonth;

        if (Storage::disk('public')->exists($folder)) {

            Storage::disk('public')->deleteDirectory($folder);

            $this->info("Folder $folder berhasil dihapus");

        } else {

            $this->info("Folder $folder tidak ditemukan");

        }

        return Command::SUCCESS;
    }
}
