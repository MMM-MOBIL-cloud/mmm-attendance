<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $user_id;
    protected $month;
    protected $year;

    public function __construct($user_id, $month, $year)
    {
        $this->user_id = $user_id;
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return Attendance::with('user')
            ->where('user_id', $this->user_id)
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->orderBy('date','asc')
            ->get();
    }

    public function headings(): array
    {
        $user = User::find($this->user_id);

        Carbon::setLocale('id');

        $bulan = Carbon::create($this->year, $this->month)->translatedFormat('F Y');

        return [
            ['LAPORAN ABSENSI'],
            ['MMM MOBIL'],
            [''],
            ['Nama : '.$user->name],
            ['Bulan : '.$bulan],
            [''],
            ['Nama','Tanggal','Hari','Jam Masuk','Jam Pulang']
        ];
    }

    public function map($attendance): array
    {
        Carbon::setLocale('id');

        $tanggal = Carbon::parse($attendance->date);

        return [
            $attendance->user->name ?? '-',
            $tanggal->translatedFormat('d F Y'),
            $tanggal->translatedFormat('l'),
            $attendance->check_in ?? '-',
            $attendance->check_out ?? '-'
        ];
    }
}
