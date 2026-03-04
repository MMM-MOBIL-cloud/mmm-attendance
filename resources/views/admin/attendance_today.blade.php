<x-app-layout>

<div class="p-6">

<h1 class="text-2xl font-bold mb-6">Absensi Hari Ini</h1>

<div class="bg-white shadow rounded-lg overflow-hidden">

<table class="min-w-full">

<thead class="bg-gray-100">
<tr>
<th class="p-3 text-left">User</th>
<th class="p-3 text-left">Tanggal</th>
<th class="p-3 text-left">Jam Masuk</th>
<th class="p-3 text-left">Jam Pulang</th>
<th class="p-3 text-left">Status</th>
</tr>
</thead>

<tbody>

@forelse($attendances as $attendance)

<tr class="border-t">

<td class="p-3">
{{ $attendance->user->name }}
</td>

<td class="p-3">
{{ $attendance->date }}
</td>

<td class="p-3 text-green-600">
{{ $attendance->check_in ?? '-' }}
</td>

<td class="p-3 text-red-600">
{{ $attendance->check_out ?? '-' }}
</td>

<td class="p-3">

@if($attendance->status == 'Terlambat')
<span class="bg-red-500 text-white px-2 py-1 rounded text-sm">
Terlambat
</span>
@else
<span class="bg-green-500 text-white px-2 py-1 rounded text-sm">
Hadir
</span>
@endif

</td>

</tr>

@empty

<tr>
<td colspan="5" class="p-4 text-center text-gray-500">
Belum ada absensi hari ini
</td>
</tr>

@endforelse

</tbody>

</table>

</div>

</div>

</x-app-layout>
