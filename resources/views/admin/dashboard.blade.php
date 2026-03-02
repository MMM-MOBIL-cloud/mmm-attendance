<x-app-layout>

<div class="p-6">

    <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>
<div class="mb-4">
    <a href="{{ route('admin.export.monthly', [now()->month, now()->year]) }}"
       class="bg-green-600 text-white px-4 py-2 rounded">
        Export Bulan Ini
    </a>
</div>
    {{-- Statistik --}}
    <div class="grid grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-600 text-white p-6 rounded-xl shadow">
            <h2 class="text-lg">Total Users</h2>
            <p class="text-3xl font-bold">{{ $totalUsers }}</p>
        </div>

        <div class="bg-green-600 text-white p-6 rounded-xl shadow">
            <h2 class="text-lg">Total Absensi</h2>
            <p class="text-3xl font-bold">{{ $totalAbsensi }}</p>
        </div>

        <div class="bg-purple-600 text-white p-6 rounded-xl shadow">
            <h2 class="text-lg">Hadir Hari Ini</h2>
            <p class="text-3xl font-bold">{{ $hadirHariIni }}</p>
        </div>
 </div>
 <div class="grid grid-cols-3 gap-6 mt-6">

    <div class="bg-green-500 text-white p-6 rounded-xl shadow">
        <h3>Hadir Bulan Ini</h3>
        <p class="text-2xl font-bold">
            {{ $totalHadirBulanIni }}
        </p>
    </div>

    <div class="bg-red-500 text-white p-6 rounded-xl shadow">
        <h3>Terlambat Bulan Ini</h3>
        <p class="text-2xl font-bold">
            {{ $totalTerlambatBulanIni }}
        </p>
    </div>

    <div class="bg-yellow-500 text-white p-6 rounded-xl shadow">
        <h3>Pulang Cepat</h3>
        <p class="text-2xl font-bold">
            {{ $totalPulangCepat }}
        </p>
    </div>

</div>
    <div class="grid grid-cols-2 gap-6 mb-8">

    <!-- Ranking Rajin -->
    <div class="bg-white p-5 rounded-xl shadow">
        <h3 class="font-semibold mb-4 text-green-600">
            🏆 Ranking Paling Rajin
        </h3>

        @foreach($rankingHadir as $index => $user)
            <div class="flex justify-between items-center border-b py-2">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-gray-400">
                        #{{ $index + 1 }}
                    </span>
                    <span>{{ $user->name }}</span>
                </div>

                <span class="font-semibold text-green-600">
                    {{ $user->total_hadir }} hari
                </span>
            </div>
        @endforeach
    </div>

    <!-- Ranking Terlambat -->
    <div class="bg-white p-5 rounded-xl shadow">
        <h3 class="font-semibold mb-4 text-red-600">
            ⚠️ Ranking Paling Sering Terlambat
        </h3>

        @foreach($rankingTerlambat as $index => $user)
            <div class="flex justify-between items-center border-b py-2">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-gray-400">
                        #{{ $index + 1 }}
                    </span>
                    <span>{{ $user->name }}</span>
                </div>

                <span class="font-semibold text-red-600">
                    {{ $user->total_terlambat }} kali
                </span>
            </div>
        @endforeach
    </div>

</div>

    {{-- Grafik --}}
    <div class="bg-white p-6 rounded-xl shadow mb-8">
        <canvas id="grafikAbsensi"></canvas>
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
<div class="bg-white p-6 rounded-xl shadow">
<table class="w-full text-center border-collapse">

    <thead class="bg-gray-100">
        <tr>
            <th class="p-3">User</th>
            <th class="p-3">Foto</th>
            <th class="p-3">Tanggal</th>
            <th class="p-3">Check In</th>
            <th class="p-3">Check Out</th>
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
    {{ $attendances->links() }}
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

</x-app-layout>
