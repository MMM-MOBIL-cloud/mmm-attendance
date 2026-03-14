<x-app-layout>

<div class="p-6">

    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>
<div class="mb-4">
    @if(session('success'))
        <div style="color:green">
            {{ session('success') }}
        </div>
    @endif
    <form method="POST" action="{{ route('invite') }}">
        @csrf

        <input type="email" name="email" placeholder="Email karyawan" required>

        <button type="submit">
            Kirim Undangan
        </button>
    <a href="{{ route('admin.export.monthly', [now()->month, now()->year]) }}"
       class="bg-green-600 text-white px-4 py-2 rounded">
        Export Bulan Ini
    </a>
    </form>
</div>

    {{-- Statistik --}}
<div class="grid grid-cols-3 gap-6 mb-8">

    <!-- Total Users -->
    <a href="{{ route('admin.users') }}">
        <div class="bg-blue-600 text-white p-6 rounded-xl shadow">
            <h3>Total Users</h3>
            <p class="text-2xl font-bold">{{ $totalUsers }}</p>
        </div>
    </a>

    <!-- Total Absensi -->
    <div class="bg-green-600 text-white p-6 rounded-xl shadow">
        <h3>Total Absensi</h3>
        <p class="text-2xl font-bold">{{ $totalAbsensi }}</p>
    </div>

    <!-- Hadir Hari Ini -->
    <a href="{{ route('admin.today') }}">
        <div class="bg-purple-600 text-white p-6 rounded-xl shadow">
            <h3>Hadir Hari Ini</h3>
            <p class="text-2xl font-bold">{{ $hadirHariIni }}</p>
        </div>
    </a>

    <!-- Jadwal Piket -->
<a href="{{ route('admin.jadwal.piket.hari.ini') }}">

    <div class="bg-yellow-500 text-white p-6 rounded-xl shadow">
        <h3>Jadwal Piket Hari Ini</h3>
        <p class="text-2xl font-bold">
            {{ $totalJadwalPiketHariIni }}
        </p>

    </div>
</a>
    <!-- Hadir Bulan -->
    <div class="bg-green-500 text-white p-6 rounded-xl shadow">
        <h3>Hadir Bulan Ini</h3>
        <p class="text-2xl font-bold">{{ $totalHadirBulanIni }}</p>
    </div>

    <!-- Terlambat -->
    <div class="bg-red-500 text-white p-6 rounded-xl shadow">
        <h3>Terlambat Bulan Ini</h3>
        <p class="text-2xl font-bold">{{ $totalTerlambatBulanIni }}</p>
    </div>
<a href="{{ route('admin.swap.index') }}"
class="inline-flex items-center px-4 py-2 ml-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow text-sm font-semibold transition">

<svg xmlns="http://www.w3.org/2000/svg"
class="h-4 w-4 mr-2"
fill="none"
viewBox="0 0 24 24"
stroke="currentColor">

<path stroke-linecap="round"
stroke-linejoin="round"
stroke-width="2"
d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>

</svg>

Tukar Jadwal

</a>
<a href="{{ route('izin.kuliah.admin') }}"
class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded shadow">

📚 Izin Kuliah

</a>
<a href="{{ route('admin.izin.index') }}"
class="bg-purple-600 text-white px-4 py-2 rounded text-center">
📝 Izin Karyawan
</a>

</div>

<div class="bg-white p-5 rounded-xl shadow mt-6">

<h3 class="font-semibold mb-4 text-blue-600">
⏱ Ranking Hadir + Jam Kerja
</h3>

@foreach($rankingJamKerja as $index => $user)

<div class="flex justify-between items-center border-b py-2">

<div>
<span class="font-bold text-gray-400">
#{{ $index + 1 }}
</span>

<span class="ml-2">
{{ $user->name }}
</span>
</div>

<div class="text-sm text-gray-600">
{{ $user->total_hadir }} hari |
{{ round($user->total_jam,2) }} jam
</div>

</div>

@endforeach
</div>

<div class="space-y-6 mt-6">

    {{-- ROW OFFICE --}}
    <div class="grid grid-cols-2 gap-4">

        {{-- Office Rajin --}}
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="font-semibold mb-3 text-green-600 text-sm lg:text-base">
                🏆 Office Paling Rajin
            </h3>

            @foreach($rankingOffice as $index => $user)

                @php
                $bg = '';
                if($index == 0) $bg = 'bg-yellow-100 border border-yellow-400';
                elseif($index == 1) $bg = 'bg-gray-100 border border-gray-400';
                elseif($index == 2) $bg = 'bg-orange-100 border border-orange-400';
                @endphp

                <div class="flex justify-between border-b py-2 px-2 rounded text-xs lg:text-sm {{ $bg }}">

                    <span>
                        @if($index == 0) 🥇
                        @elseif($index == 1) 🥈
                        @elseif($index == 2) 🥉
                        @endif

                        #{{ $index + 1 }} {{ $user->name }}
                    </span>

                    <span class="text-green-600 font-semibold">
                        {{ $user->total_hadir }}h | {{ round($user->total_jam,1) }}j
                    </span>

                </div>

                @endforeach
        </div>

        {{-- Office Terlambat --}}
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="font-semibold mb-3 text-red-600 text-sm lg:text-base">
                ⚠ Office Paling Telat
            </h3>

            @foreach($rankingOfficeLate as $index => $item)

            @php
            $border = '';
            if($index == 0) $border = 'border-l-4 border-red-500 bg-red-50';
            elseif($index == 1) $border = 'border-l-4 border-yellow-500 bg-yellow-50';
            elseif($index == 2) $border = 'border-l-4 border-orange-500 bg-orange-50';
            @endphp

            <div class="flex justify-between py-2 px-2 mb-1 rounded text-xs lg:text-sm {{ $border }}">

                <span>
                    @if($index == 0) 🚨
                    @elseif($index == 1) ⏰
                    @elseif($index == 2) ⚠
                    @endif

                    #{{ $index+1 }} {{ $item['user']->name }}
                </span>

                <span class="text-red-600 font-semibold">
                    {{ $item['days'] }}h |
                    {{ round(($item['hours'] + ($item['minutes'] / 60)),1) }}j
                </span>

            </div>

            @endforeach
        </div>

    </div>


    {{-- ROW SALES --}}
    <div class="grid grid-cols-2 gap-4">

        {{-- Sales Rajin --}}
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="font-semibold mb-3 text-blue-600 text-sm lg:text-base">
                🏆 Sales Paling Rajin
            </h3>

            @foreach($rankingSales as $index => $user)

                @php
                $bg = '';
                if($index == 0) $bg = 'bg-yellow-100 border border-yellow-400';
                elseif($index == 1) $bg = 'bg-gray-100 border border-gray-400';
                elseif($index == 2) $bg = 'bg-orange-100 border border-orange-400';
                @endphp

                <div class="flex justify-between border-b py-2 px-2 rounded text-xs lg:text-sm {{ $bg }}">

                    <span>
                        @if($index == 0) 🥇
                        @elseif($index == 1) 🥈
                        @elseif($index == 2) 🥉
                        @endif

                        #{{ $index + 1 }} {{ $user->name }}
                    </span>
                    <span class="text-blue-600 font-semibold">
                        {{ $user->total_hadir }}h | {{ round($user->total_jam,1) }}j
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Sales Terlambat --}}
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="font-semibold mb-3 text-red-600 text-sm lg:text-base">
                ⚠ Sales Paling Telat
            </h3>

            @foreach($rankingSalesLate as $index => $item)

            @php
            $border = '';
            if($index == 0) $border = 'border-l-4 border-red-500 bg-red-50';
            elseif($index == 1) $border = 'border-l-4 border-yellow-500 bg-yellow-50';
            elseif($index == 2) $border = 'border-l-4 border-orange-500 bg-orange-50';
            @endphp

            <div class="flex justify-between py-2 px-2 mb-1 rounded text-xs lg:text-sm {{ $border }}">

                <span>
                    @if($index == 0) 🚨
                    @elseif($index == 1) ⏰
                    @elseif($index == 2) ⚠
                    @endif

                    #{{ $index+1 }} {{ $item['user']->name }}
                </span>

                <span class="text-red-600 font-semibold">
                    {{ $item['days'] }}h |
                    {{ round(($item['hours'] + ($item['minutes'] / 60)),1) }}j
                </span>

            </div>

            @endforeach
        </div>

    </div>

</div>

    {{-- Kalender --}}
    <div class="bg-white p-6 rounded-xl shadow mt-8">

    <div class="flex justify-between items-center mb-4">

<h2 class="text-xl font-bold">
📅 Jadwal Karyawan
</h2>

<a href="{{ route('admin.holidays') }}"
       class="bg-blue-600 hover:bg-indigo-700 text-white
              px-4 py-2 rounded-lg shadow-lg z-50 relative">
        ⚙️ Kelola Libur
    </a>

</div>

    <div id="calendar"></div>

</div>

<div style="display:flex; gap:20px; margin-bottom:20px;">

    <div style="background:#22c55e; padding:15px; color:white; border-radius:10px;">
        <strong>Hadir Bulan Ini</strong><br>
        {{ $totalHadirBulanIni }}
    </div>

    <div style="background:#ef4444; padding:15px; color:white; border-radius:10px;">
        <strong>Terlambat Bulan Ini</strong><br>
        {{ $totalTerlambatBulanIni }}
    </div>

    <div style="background:#f59e0b; padding:15px; color:white; border-radius:10px;">
        <strong>Belum Pulang Hari Ini</strong><br>
        {{ $totalBelumPulangHariIni }}
    </div>

</div>

    {{-- Tabel --}}
    <h2 class="text-xl font-bold mb-4">
Riwayat Absensi Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
</h2>
    <form method="GET" class="mb-4 grid grid-cols-2 lg:flex gap-3">

<select name="user_id" class="border rounded px-3 py-2">
<option value="">Semua User</option>

@foreach($users as $user)
<option value="{{ $user->id }}"
{{ request('user_id') == $user->id ? 'selected' : '' }}>
{{ $user->name }}
</option>
@endforeach

</select>
<select name="month" class="border rounded px-3 py-2">

<option value="">Semua Bulan</option>

@for($m=1;$m<=12;$m++)
<option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
</option>
@endfor

</select>
<select name="year" class="border rounded px-3 py-2">

<option value="">Semua Tahun</option>

@for($y=2024;$y<=2035;$y++)
<option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
{{ $y }}
</option>
@endfor

</select>

<input type="date"
name="date"
value="{{ request('date') }}"
class="border rounded px-3 py-2">

<button class="bg-blue-600 text-white px-4 py-2 rounded">
Filter
</button>

<a href="{{ route('admin.dashboard') }}"
class="bg-gray-500 text-white px-4 py-2 rounded">
Reset
</a>

</form>
<div class="bg-white p-6 rounded-xl shadow">

<div class="overflow-x-auto">
<table class="min-w-full text-center border-collapse">

    <thead class="bg-gray-100">
        <tr>
            <th class="p-3">User</th>
            <th class="p-3">Foto</th>
            <th class="p-3">Tanggal</th>
            <th class="p-3">Check In</th>
            <th class="p-3">Check Out</th>
            <th class="p-3">Checkout Type</th>
            <th class="p-3">Status</th>
            <th class="p-3">Jam Kerja</th>
            <th class="p-3">Jarak</th>
            <th class="p-3">Approval Masuk</th>
            <th class="p-3">Approval Pulang</th>
            <th class="p-3">Action Masuk</th>
            <th class="p-3">Action Pulang</th>
        </tr>
    </thead>

    <tbody>
    @foreach ($attendances as $attendance)
    <tr class="border-b">

        {{-- USER --}}
        <td class="p-3">
            {{ $attendance->user->name }}
        </td>

        {{-- FOTO --}}
        <td class="p-3">
            @if($attendance->photo)
                <img src="{{ asset('storage/'.$attendance->photo) }}"
                     width="60"
                     class="rounded-lg shadow">
            @else
                -
            @endif
        </td>

        {{-- TANGGAL --}}
        <td class="p-3">
            {{ $attendance->date }}
        </td>

        {{-- CHECK IN --}}
        <td class="p-3">
            {{ $attendance->check_in ?? '-' }}
        </td>

        {{-- CHECK OUT --}}
        <td class="p-3">
            {{ $attendance->check_out ?? '-' }}
        </td>

        {{-- CHECKOUT TYPE --}}
       <td class="p-3">

@if(!$attendance->check_out)
    -
@else

    @php
        $type = strtolower(trim($attendance->checkout_type ?? ''));
    @endphp

    @if($type == 'auto')
        <span style="background:#f97316;color:white;padding:3px 10px;border-radius:9999px;font-size:12px;">
            AUTO
        </span>

    @elseif($type == 'manual')
        <span style="background:#3b82f6;color:white;padding:3px 10px;border-radius:9999px;font-size:12px;">
            MANUAL
        </span>

    @else
        -
    @endif

@endif

</td>

        {{-- STATUS --}}
        <td class="p-3">
            @php
                $status = '-';
                $color = 'bg-gray-400';

                if ($attendance->check_in) {
                    $jamMasuk = \Carbon\Carbon::parse($attendance->check_in);
                    $batasMasuk = \Carbon\Carbon::parse(config('app.work_time.start'))
                        ->addMinutes(config('app.work_time.tolerance_minutes'));

                    if ($attendance->check_out === null) {
                        $status = 'Belum Pulang';
                        $color = 'bg-yellow-500';
                    } elseif ($jamMasuk->gt($batasMasuk)) {
                        $status = 'Terlambat';
                        $color = 'bg-red-500';
                    } else {
                        $status = 'Hadir';
                        $color = 'bg-green-500';
                    }
                }
            @endphp

            <span class="text-white px-3 py-1 rounded-full text-sm {{ $color }}">
                {{ $status }}
            </span>
        </td>

        {{-- JAM KERJA --}}
        <td class="p-3 whitespace-nowrap">
            @if($attendance->check_in && $attendance->check_out)
                @php
                    $masuk = \Carbon\Carbon::parse($attendance->check_in);
                    $pulang = \Carbon\Carbon::parse($attendance->check_out);
                    $durasi = $masuk->diff($pulang);
                @endphp

                {{ $durasi->format('%H jam %I menit') }}
            @else
                -
            @endif
        </td>

        {{-- JARAK --}}
        <td class="p-3">
            @if($attendance->latitude && $attendance->longitude)
                @php
                    $officeLat = config('app.office_location.latitude');
                    $officeLng = config('app.office_location.longitude');
                    $earthRadius = 6371000;

                    $dLat = deg2rad($officeLat - $attendance->latitude);
                    $dLng = deg2rad($officeLng - $attendance->longitude);

                    $a = sin($dLat/2) * sin($dLat/2) +
                         cos(deg2rad($attendance->latitude)) *
                         cos(deg2rad($officeLat)) *
                         sin($dLng/2) * sin($dLng/2);

                    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                    $distance = $earthRadius * $c;
                @endphp

                <a href="https://www.google.com/maps?q={{ $attendance->latitude }},{{ $attendance->longitude }}"
   target="_blank"
   class="text-blue-600 underline">
    {{ round($distance) }} m
</a>
            @else
                -
            @endif
        </td>

{{-- APPROVAL MASUK --}}
    <td class="p-3">
        <span class="px-3 py-1 rounded-full text-white
            {{ $attendance->approval_status == 'Approved' ? 'bg-green-500' :
               ($attendance->approval_status == 'Rejected' ? 'bg-red-500' : 'bg-yellow-500') }}">
            {{ $attendance->approval_status }}
        </span>
    </td>

    {{-- APPROVAL PULANG --}}
    <td class="p-3">
        @if($attendance->check_out)
            <span class="px-3 py-1 rounded-full text-white
                {{ $attendance->checkout_approval_status == 'Approved' ? 'bg-green-500' :
                   ($attendance->checkout_approval_status == 'Rejected' ? 'bg-red-500' : 'bg-yellow-500') }}">
                {{ $attendance->checkout_approval_status }}
            </span>
        @else
            -
        @endif
    </td>

    {{-- ACTION MASUK --}}
    <td class="p-3">
        @if($attendance->approval_status == 'Pending')
            <form action="{{ route('attendance.approve', $attendance->id) }}" method="POST" class="inline">
                @csrf
                <button class="bg-green-600 text-white px-3 py-1 rounded text-sm">
                    Approve
                </button>
            </form>

            <form action="{{ route('attendance.reject', $attendance->id) }}" method="POST" class="inline">
                @csrf
                <button class="bg-red-600 text-white px-3 py-1 rounded text-sm">
                    Reject
                </button>
            </form>
        @else
            -
        @endif
    </td>

    {{-- ACTION PULANG --}}
    <td class="p-3">

    @if($attendance->check_out)

        @php
            $checkoutStatus = strtolower($attendance->checkout_approval_status ?? '');
        @endphp

        @if($checkoutStatus === 'pending')

            <form action="{{ route('attendance.approve.checkout', $attendance->id) }}" method="POST" class="inline">
                @csrf
                <button class="bg-green-600 text-white px-3 py-1 rounded text-sm">
                    Approve
                </button>
            </form>

            <form action="{{ route('attendance.reject.checkout', $attendance->id) }}" method="POST" class="inline">
                @csrf
                <button class="bg-red-600 text-white px-3 py-1 rounded text-sm">
                    Reject
                </button>
            </form>

        @else
            -
        @endif

    @else
        -
    @endif

</td>

</tr>
    @endforeach
    </tbody>

</table>

<div class="mt-4">
    {{ $attendances->appends(request()->query())->links() }}
</div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('grafikAbsensi');

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, '#6366F1');
    gradient.addColorStop(1, '#8B5CF6');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                'Jan','Feb','Mar','Apr','Mei','Jun',
                'Jul','Agu','Sep','Okt','Nov','Des'
            ],
            datasets: [{
                label: 'Total Absensi per Bulan',
                data: {!! json_encode(array_values($grafikBulanan)) !!},
                backgroundColor: gradient,
                borderRadius: 12,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' absensi';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 650,
        events: '/admin/calendar-events',
        dayMaxEvents: true,

        dateClick: function(info) {

    document.getElementById('modalLibur').style.display = 'flex';

    document.getElementById('tanggalDipilih').value = info.dateStr;

},
    });

    calendar.render();

});

function tutupModal(){
    document.getElementById('modalLibur').style.display = 'none';
}

function simpanLibur(){

    fetch('/admin/calendar/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            title: document.getElementById('judulLibur').value,
            date: document.getElementById('tanggalDipilih').value
        })
    })
    .then(res => res.json())
    .then(data => {

        if(data.success){

            alert('Libur berhasil ditambahkan');

            document.getElementById('modalLibur').style.display='none';

            location.reload();

        }else{
            alert('Gagal menyimpan');
        }

    });

}
</script>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<!-- Modal Tambah Libur -->
<div id="modalLibur" style="display:none;
position:fixed; top:0; left:0; right:0; bottom:0;
background:rgba(0,0,0,0.5);
align-items:center; justify-content:center; z-index:9999;">

    <div style="background:white; padding:20px; border-radius:10px; width:350px;">
        <h3 style="font-weight:bold; margin-bottom:10px;">Tambah Event</h3>

        <form id="formLibur" method="POST" onsubmit="return false;">
            <input type="hidden" id="tanggalDipilih">

            <label>Judul</label>
            <input type="text" id="judulLibur"
                   style="width:100%; border:1px solid #ddd; padding:8px; border-radius:6px; margin-bottom:10px">

            <button type="button" onclick="simpanLibur()"
                    style="background:#22c55e; color:white; padding:8px 12px; border:none; border-radius:6px;">
                Simpan
            </button>

            <button type="button" onclick="tutupModal()"
                    style="background:#ef4444; color:white; padding:8px 12px; border:none; border-radius:6px;">
                Batal
            </button>
        </form>
    </div>
</div>

</x-app-layout>
