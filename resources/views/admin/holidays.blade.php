<x-app-layout>

<div class="p-6">

<h2 class="text-2xl font-bold mb-4">Kelola Libur Nasional</h2>

<form action="/admin/holidays/store" method="POST" class="mb-6">
    @csrf

    <div class="flex gap-3">
        <input type="text" name="title" placeholder="Judul libur"
               class="border px-3 py-2 rounded w-64" required>

        <input type="date" name="date"
               class="border px-3 py-2 rounded" required>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Simpan
        </button>
    </div>
</form>

</div>

</x-app-layout>
