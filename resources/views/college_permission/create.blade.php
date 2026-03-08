<x-app-layout>

<div class="p-6">

<h2 class="text-xl font-bold mb-6">Ajukan Izin Kuliah</h2>
<p class="text-sm text-gray-600 mb-6">
Jika Anda memiliki jadwal kuliah pada jam kerja, silakan ajukan izin.
Anda dapat mengganti jam kerja setelah kuliah (opsional).
</p>

<form method="POST" action="{{ route('izin.kuliah.store') }}">
@csrf

<h3 class="font-semibold mb-4 text-purple-600">Izin Kuliah</h3>

<div class="grid grid-cols-2 gap-4">

<div>
<label class="block text-sm font-medium">Tanggal Kuliah</label>
<input type="date" name="date" required class="border p-2 w-full rounded">
</div>

<div>
<label class="block text-sm font-medium">Jam Mulai Kuliah</label>
<input type="time" name="start_time" required class="border p-2 w-full rounded">
</div>

<div>
<label class="block text-sm font-medium">Jam Selesai Kuliah</label>
<input type="time" name="end_time" required class="border p-2 w-full rounded">
</div>

<div>
<label class="block text-sm font-medium">Keterangan Kuliah</label>
<input type="text" name="reason" placeholder="Contoh: Kuliah Sistem Informasi" class="border p-2 w-full rounded">
</div>

</div>

<p class="font-semibold mt-8 mb-4 text-green-600">Ganti Jam Kerja (Opsional)</h3>
<p class="text-xs text-gray-500 mb-2">
Isi bagian ini jika Anda ingin mengganti jam kerja setelah selesai kuliah.
</p>
<div class="grid grid-cols-2 gap-4">

<div>
<label class="block text-sm font-medium">Tanggal Ganti Kerja</label>
<input type="date" name="replace_date" class="border p-2 w-full rounded">
</div>

<div>
<label class="block text-sm font-medium">Jam Mulai Ganti Kerja</label>
<input type="time" name="replace_start" class="border p-2 w-full rounded">
</div>

<div>
<label class="block text-sm font-medium">Jam Selesai Ganti Kerja</label>
<input type="time" name="replace_end" class="border p-2 w-full rounded">
</div>

<div>
<label class="block text-sm font-medium">Keterangan Ganti Kerja</label>
<input type="text" name="replace_reason" placeholder="Contoh: Ganti jam kuliah" class="border p-2 w-full rounded">
</div>

</div>

<button class="mt-8 bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
Kirim Izin
</button>

</form>

</div>

</x-app-layout>
