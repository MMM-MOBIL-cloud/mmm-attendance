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
    <p class="text-gray-500 text-sm mt-1">
        Sistem Absensi MMM MOBIL
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">

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
</div>

</div>
<div class="p-6 text-gray-900">

    <form method="POST" action="{{ route('check.in') }}">
    @csrf

    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">
    <input type="hidden" name="photo" id="photoInput">

    @if(session('error'))
        <div class="mt-2 text-red-600">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-4">
        <video id="video" width="300" autoplay></video>
        <canvas id="canvas" style="display:none;"></canvas>

        <button type="button" onclick="takePhoto()">
            Ambil Foto
        </button>
    </div>

    <button type="submit"
    class="bg-blue-600 text-white px-4 py-2 rounded"
    {{
        $attendanceToday
        && $attendanceToday->check_in
        && $attendanceToday->approval_status != 'Rejected'
        ? 'disabled'
        : ''
    }}>
    Absen Masuk
</button>
</form>

@if(session('success'))
    <div class="mt-2 text-green-600">
        {{ session('success') }}
    </div>
@endif

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
</div><div class="mt-10">
    <h2 class="text-xl font-semibold mb-4">Riwayat Absensi</h2>

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
</div>
            </div>
        </div>
    </div>
    <script>
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const photoInput = document.getElementById('photoInput');

// Aktifkan Kamera
navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
        video.srcObject = stream;
    })
    .catch(err => {
        alert("Kamera tidak bisa diakses");
    });

// Ambil Foto
function takePhoto() {
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    context.drawImage(video, 0, 0);

    const imageData = canvas.toDataURL('image/png');
    photoInput.value = imageData;

    alert("Foto berhasil diambil");
}

// Ambil Lokasi GPS
navigator.geolocation.getCurrentPosition(function(position) {

    // Untuk Check-In
    const latIn = document.getElementById('latitude');
    const lngIn = document.getElementById('longitude');

    if (latIn && lngIn) {
        latIn.value = position.coords.latitude;
        lngIn.value = position.coords.longitude;
    }

    // Untuk Check-Out
    const latOut = document.getElementById('latitudeOut');
    const lngOut = document.getElementById('longitudeOut');

    if (latOut && lngOut) {
        latOut.value = position.coords.latitude;
        lngOut.value = position.coords.longitude;
    }

});
</script>
</x-app-layout>
