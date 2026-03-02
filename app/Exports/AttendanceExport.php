<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    public function collection()
    {
        return Attendance::with('user')
            ->orderBy('date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang'
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->user->name ?? '-',
            \Carbon\Carbon::parse($attendance->date)->format('d M Y'),
            $attendance->check_in,
            $attendance->check_out ?? '-'
        ];
    }
}
