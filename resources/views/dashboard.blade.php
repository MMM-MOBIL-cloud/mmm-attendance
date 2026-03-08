<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
    <div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">
        Selamat Datang, {{ auth()->user()->name }} 👋
    </h2>

@if(session('error'))
<div class="bg-red-500 text-white px-4 py-3 rounded mt-4">
    {{ session('error') }}
</div>
@endif

@if(session('success'))
<div class="bg-green-500 text-white px-4 py-3 rounded mt-4">
    {{ session('success') }}
</div>
@endif
    <p class="text-gray-500 text-sm mt-1">
        Sistem Absensi MMM MOBIL
    </p>
    <p class="text-gray-500 text-sm mt-1">
    Batas Jam Check-in : 07:45 – 11:00 /
    Jam Kerja    : 08:00 – 16:00
    </p>
</div>

    {{-- 🔔 NOTIFIKASI REJECT --}}
    @if(auth()->user()->unreadNotifications->count() > 0)
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 mt-4">
        @foreach(auth()->user()->unreadNotifications as $notification)
            <p>🔔 {{ $notification->data['message'] }}</p>
        @endforeach
    </div>

    {{-- Tandai semua notifikasi sebagai sudah dibaca --}}
    {{ auth()->user()->unreadNotifications->markAsRead() }}
@endif
               <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="bg-white shadow-lg rounded-2xl p-6">
                    <h2 class="text-lg font-semibold text-gray-700">Absen Hari Ini</h2>
                    <p class="text-3xl font-bold text-blue-600 mt-2">
                        {{ \Carbon\Carbon::now()->format('d M Y') }}
                    </p>
                    @if($attendanceToday && $attendanceToday->status)
    <p class="mt-2 text-sm font-semibold
        {{ $attendanceToday->status == 'Terlambat' ? 'text-red-600' : 'text-green-600' }}">

        {{ $attendanceToday->status == 'Terlambat' ? '🔴 Terlambat' : '🟢 Hadir' }}
    </p>
@endif
                </div>

                <div class="bg-white shadow-lg rounded-2xl p-6">
                    <h2 class="text-lg font-semibold text-gray-700">Jam Masuk</h2>
                    <p class="text-3xl font-bold text-green-600 mt-2">
                        {{ $attendanceToday->check_in ?? '-' }}
                    </p>
                </div>

                <div class="bg-white shadow-lg rounded-2xl p-6">
                    <h2 class="text-lg font-semibold text-gray-700">Jam Pulang</h2>
                    <p class="text-3xl font-bold text-red-600 mt-2">
                        {{ $attendanceToday->check_out ?? '-' }}
                    </p>
                </div>
    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-gray-500 text-sm">Total Hadir Bulan Ini</h3>
        <p class="text-3xl font-bold text-blue-600 mt-2">
            {{ $totalHadirBulanIni }} Hari
        </p>
    </div>
    <div class="bg-white shadow-md rounded-lg p-6">
        <h3 class="text-gray-500 text-sm">Belum Absen Pulang</h3>
        <p class="text-3xl font-bold text-red-600 mt-2">
            {{ $totalBelumPulang }} Hari
        </p>
    </div>
    <div class="bg-white shadow-md rounded-lg p-6">
    <h3 class="text-gray-500 text-sm">Total Terlambat Bulan Ini</h3>
    <p class="text-3xl font-bold text-red-600 mt-2">
        {{ $totalTerlambatBulanIni }} Hari
    </p>
</div>
<div class="bg-white shadow-md rounded-lg p-6">
    <h3 class="text-gray-500 text-sm">Total Jam Terlambat Bulan Ini</h3>
    <p class="text-2xl font-bold text-orange-600 mt-2">
        {{ $totalJamTerlambat }} Jam {{ $sisaMenitTerlambat }} Menit
    </p>
</div>
</div>

</div>
<div class="p-6 text-gray-900">

    @php
$todayName = now()->format('l');

$workDays = DB::table('user_work_days')
    ->where('user_id', auth()->id())
    ->pluck('day')
    ->toArray();

$isWorkDay = in_array($todayName, $workDays);
@endphp

@if(!$isWorkDay)
<div style="background:#FEF3C7; border:1px solid #F59E0B; color:#92400E; padding:10px; border-radius:8px; margin-bottom:15px;">
⚠️ Hari ini bukan jadwal kerja anda.
</div>
@endif

    @if($isWorkDay && (!$attendanceToday || $attendanceToday->approval_status == 'Rejected'))
<form method="POST" action="{{ route('check.in') }}">
    @csrf

    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">
    <input type="hidden" name="photo" id="photoInput">

    <div class="mb-4">

<button type="button"
    onclick="showCameraSection()"
    class="bg-blue-600 text-white px-4 py-2 rounded">
    Absen Masuk
</button>

<div id="cameraSection" style="display:none; margin-top:20px">

    <button type="button"
        onclick="startCamera()"
        class="bg-gray-700 text-white px-4 py-2 rounded">
        Aktifkan Kamera
    </button>

    <br><br>

    <video id="video" width="300" autoplay playsinline
    style="display:none;border-radius:10px"></video>

    <canvas id="canvas" style="display:none;"></canvas>

    <img id="previewImage" style="display:none; width:300px; border-radius:10px; margin-top:10px;">

    <br><br>

    <button type="button"
        onclick="takePhoto()"
        id="captureBtn"
        class="bg-green-600 text-white px-4 py-2 rounded"
        style="display:none">
        Ambil Foto
    </button>

    <button type="button"
    onclick="retakePhoto()"
    id="retakeBtn"
    class="bg-yellow-500 text-white px-4 py-2 rounded"
    style="display:none">
    Ambil Ulang
</button>

    <br><br>

    <button type="submit"
        class="bg-blue-600 text-white px-4 py-2 rounded">
        Kirim Absen
    </button>

</div>
</form>

@if(session('success'))
    <div class="mt-2 text-green-600">
        {{ session('success') }}
    </div>
@endif

</form>
@endif

@if($isWorkDay)
<form method="POST" action="{{ route('check.out') }}" class="mt-2">
    @csrf
    <input type="hidden" name="latitude" id="latitudeOut">
    <input type="hidden" name="longitude" id="longitudeOut">
    <button
        type="submit"
        class="bg-red-500 text-white px-4 py-2 rounded disabled:opacity-50"
        {{ $attendanceToday && $attendanceToday->check_out ? 'disabled' : '' }}>
        Absen Pulang
    </button>
</form>
@endif

</div>

<div class="mt-10 flex justify-center gap-4">

<a href="{{ route('swap.index') }}"
   class="inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow">

    Tukar Jadwal
</a>

<a href="{{ route('swap.approval.index') }}"
   class="inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow">

    Approval Tukar Jadwal
</a>

@if(Auth::user()->is_student)
<a href="/izin-kuliah"
   class="inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow">

    Ajukan Izin Kuliah
</a>
@endif

</div>

</div>

<h2 class="text-xl font-semibold text-center mt-10 mb-4">
    Riwayat Absensi Bulan {{ now()->translatedFormat('F Y') }}
</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow-md rounded-lg">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Jam Masuk</th>
                    <th class="px-4 py-2">Jam Pulang</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendanceHistory as $attendance)
                    <tr class="border-t">
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-2 text-green-600">
                            {{ $attendance->check_in ?? '-' }}
                        </td>
                        <td class="px-4 py-2 text-red-600">
                            {{ $attendance->check_out ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                            Belum ada riwayat absensi
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(Auth::user()->is_student)

    <h2 class="text-xl font-semibold text-center mt-10 mb-4">
Riwayat Izin Kuliah
</h2>

<div class="overflow-x-auto">
<table class="min-w-full bg-white shadow-md rounded-lg">

<thead>
<tr class="bg-gray-100 text-left">
<th class="px-4 py-2">Tanggal</th>
<th class="px-4 py-2">Jam Kuliah</th>
<th class="px-4 py-2">Status</th>
</tr>
</thead>

<tbody>

@forelse($izinKuliahHistory as $izin)

<tr class="border-t">

<td class="px-4 py-2">
{{ \Carbon\Carbon::parse($izin->date)->format('d M Y') }}
</td>

<td class="px-4 py-2">
{{ $izin->start_time }} - {{ $izin->end_time }}
</td>

<td class="px-4 py-2">

@if($izin->status == 'pending')
<span class="bg-yellow-500 text-white px-2 py-1 rounded text-sm">
Pending
</span>

@elseif($izin->status == 'approved')
<span class="bg-green-600 text-white px-2 py-1 rounded text-sm">
Approved
</span>

@else
<span class="bg-red-600 text-white px-2 py-1 rounded text-sm">
Rejected
</span>
@endif

</td>

</tr>

@empty

<tr>
<td colspan="3" class="px-4 py-4 text-center text-gray-500">
Belum ada riwayat izin kuliah
</td>
</tr>

@endforelse

</tbody>
</table>
</div>

@endif

</div>
            </div>
        </div>
    </div>
    <script>

let stream;

function showCameraSection(){
    document.getElementById("cameraSection").style.display = "block";
}

function startCamera(){

    navigator.mediaDevices.getUserMedia({
        video: { facingMode: "user" }
    })
    .then(function(s){

        stream = s;

        const video = document.getElementById('video');
        const captureBtn = document.getElementById('captureBtn');

        video.srcObject = stream;

        video.style.display = 'block';
        captureBtn.style.display = 'inline-block';

    })
    .catch(function(){

        alert("Kamera tidak bisa diakses");

    });

}

function takePhoto(){

const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const preview = document.getElementById('previewImage');
const photoInput = document.getElementById('photoInput');

const context = canvas.getContext('2d');

canvas.width = video.videoWidth;
canvas.height = video.videoHeight;

context.drawImage(video,0,0);

const imageData = canvas.toDataURL('image/png');

photoInput.value = imageData;

preview.src = imageData;
preview.style.display = "block";

video.style.display = "none";

document.getElementById("captureBtn").style.display = "none";
document.getElementById("retakeBtn").style.display = "inline-block";

}

function retakePhoto(){

const video = document.getElementById('video');
const preview = document.getElementById('previewImage');

preview.style.display = "none";

video.style.display = "block";

document.getElementById("captureBtn").style.display = "inline-block";
document.getElementById("retakeBtn").style.display = "none";

}

// Ambil Lokasi GPS
navigator.geolocation.getCurrentPosition(function(position) {

    const latIn = document.getElementById('latitude');
    const lngIn = document.getElementById('longitude');

    if (latIn && lngIn) {
        latIn.value = position.coords.latitude;
        lngIn.value = position.coords.longitude;
    }

    const latOut = document.getElementById('latitudeOut');
    const lngOut = document.getElementById('longitudeOut');

    if (latOut && lngOut) {
        latOut.value = position.coords.latitude;
        lngOut.value = position.coords.longitude;
    }

});

</script>
</x-app-layout>
