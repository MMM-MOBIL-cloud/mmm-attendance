<x-app-layout>

<div class="p-6">

<h1 class="text-2xl font-bold mb-6">
Ajukan Izin
</h1>

<form method="POST" action="{{ route('izin.store') }}" enctype="multipart/form-data">

@csrf

<div class="mb-4">
<label class="block font-semibold mb-1">Tanggal</label>
<input type="date" name="date" class="border p-2 rounded w-full" required>
</div>

<div class="mb-4">
<label class="block font-semibold mb-1">Jam Mulai</label>
<input type="time" name="start_time" class="border p-2 rounded w-full">
</div>

<div class="mb-4">
<label class="block font-semibold mb-1">Jam Selesai</label>
<input type="time" name="end_time" class="border p-2 rounded w-full">
</div>

<div class="mb-4">
<label class="block font-semibold mb-1">Alasan</label>
<textarea name="reason" class="border p-2 rounded w-full" required></textarea>
</div>

<div class="mb-4">
<label class="block font-semibold mb-1">Upload Bukti (Jika dibutuhkan)</label>
<input type="file" name="proof" class="border p-2 rounded w-full">
</div>

<button class="bg-blue-600 text-white px-4 py-2 rounded">
Kirim Pengajuan Izin
</button>

<a href="{{ route('izin.index') }}"
class="ml-2 bg-gray-500 text-white px-4 py-2 rounded">
Kembali
</a>

</form>

</div>

</x-app-layout>
