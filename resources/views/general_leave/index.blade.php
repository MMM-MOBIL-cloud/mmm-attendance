<x-app-layout>

<div class="p-6">

<h1 class="text-2xl font-bold mb-6">
Riwayat Izin
</h1>

<a href="{{ route('izin.create') }}"
class="bg-blue-600 text-white px-4 py-2 rounded">
Ajukan Izin
</a>

<div class="mt-6 overflow-x-auto">

<table class="min-w-full bg-white shadow rounded">

<thead>
<tr class="bg-gray-100">
<th class="px-4 py-2">Tanggal</th>
<th class="px-4 py-2">Jam</th>
<th class="px-4 py-2">Alasan</th>
<th class="px-4 py-2">Bukti</th>
<th class="px-4 py-2">Status</th>
</tr>
</thead>

<tbody>

@forelse($leaves as $leave)

<tr class="border-t">

<td class="px-4 py-2">
{{ \Carbon\Carbon::parse($leave->date)->format('d M Y') }}
</td>

<td class="px-4 py-2">
{{ $leave->start_time }} - {{ $leave->end_time }}
</td>

<td class="px-4 py-2">
{{ $leave->reason }}
</td>

<td class="px-4 py-2">

@if($leave->proof)

<a href="{{ asset('storage/leave_proofs/'.$leave->proof) }}"
   target="_blank"
   class="bg-blue-600 text-white px-2 py-1 rounded text-sm">

Lihat Bukti

</a>

@else

<span class="text-gray-400">Tidak ada</span>

@endif

</td>

<td class="px-4 py-2">

@if($leave->status == 'pending')
<span class="bg-yellow-500 text-white px-2 py-1 rounded text-sm">
Pending
</span>

@elseif($leave->status == 'approved')
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
<td colspan="4" class="text-center py-4">
Belum ada pengajuan izin
</td>
</tr>

@endforelse

</tbody>

</table>

</div>

</div>

</x-app-layout>
