<x-app-layout>

<div class="p-6">

<h2 class="text-2xl font-bold mb-4">📢 Kalender Pengumuman</h2>

<a href="{{ route('announcements.create') }}"
class="bg-green-500 text-white px-4 py-2 rounded">
+ Tambah Pengumuman
</a>

<div class="mt-6 bg-white rounded-xl shadow">

<table class="w-full">

<thead class="border-b">
<tr class="text-left">
<th class="p-3">Judul</th>
<th>Tanggal</th>
<th>Tipe</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

@foreach($events as $event)
<tr class="border-b">
<td class="p-3">{{ $event->title }}</td>
<td>{{ $event->start_date }}</td>
<td>{{ $event->type }}</td>
<td>

<form method="POST"
action="{{ route('announcements.destroy',$event->id) }}">
@csrf
@method('DELETE')

<button class="text-red-500">Hapus</button>

</form>

</td>
</tr>
@endforeach

</tbody>

</table>

</div>

</div>

</x-app-layout>
