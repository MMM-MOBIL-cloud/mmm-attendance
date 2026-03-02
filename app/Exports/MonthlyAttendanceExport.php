<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MonthlyAttendanceExport implements FromCollection, WithHeadings
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return Attendance::with('user')
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->get()
            ->map(function ($attendance) {
                return [
                    'Nama' => $attendance->user->name,
                    'Tanggal' => $attendance->date,
                    'Check In' => $attendance->check_in,
                    'Check Out' => $attendance->check_out,
                    'Status' => $attendance->status,
                    'Jam Kerja (jam)' => $attendance->work_hours,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Tanggal',
            'Check In',
            'Check Out',
            'Status',
            'Jam Kerja (jam)'
        ];
    }
}
