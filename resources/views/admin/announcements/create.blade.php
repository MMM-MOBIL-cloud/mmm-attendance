<x-app-layout>

<div class="p-6">

<h2 class="text-2xl font-bold mb-4">Tambah Pengumuman</h2>

<form method="POST" action="{{ route('announcements.store') }}"
class="bg-white p-6 rounded-xl shadow space-y-4">

@csrf

<input type="text" name="title"
placeholder="Judul"
class="w-full border p-2 rounded">

<textarea name="description"
placeholder="Deskripsi"
class="w-full border p-2 rounded"></textarea>

<div class="grid grid-cols-2 gap-4">

<input type="date" name="start_date"
class="border p-2 rounded">

<input type="date" name="end_date"
class="border p-2 rounded">

</div>

<input type="text" name="type"
placeholder="Tipe (Libur / Event / Meeting)"
class="w-full border p-2 rounded">

<input type="color" name="color"
value="#22c55e">

<button class="bg-blue-500 text-white px-4 py-2 rounded">
Simpan
</button>

</form>

</div>

</x-app-layout>
