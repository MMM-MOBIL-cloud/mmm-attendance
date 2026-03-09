<x-app-layout>

<div class="p-6">

<h1 class="text-2xl font-bold mb-6">
Daftar Pengajuan Izin
</h1>

<div class="overflow-x-auto">

<table class="min-w-full bg-white shadow rounded">

<thead>
<tr class="bg-gray-100">
<th class="px-4 py-2">User</th>
<th class="px-4 py-2">Tanggal</th>
<th class="px-4 py-2">Jam</th>
<th class="px-4 py-2">Alasan</th>
<th class="px-4 py-2">Bukti</th>
<th class="px-4 py-2">Status</th>
<th class="px-4 py-2">Action</th>
</tr>
</thead>

<tbody>

@foreach($leaves as $leave)

<tr class="border-t">

<td class="px-4 py-2">
{{ $leave->user->name }}
</td>

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
{{ $leave->status }}
</td>

<td class="px-4 py-2 flex gap-2">

@if($leave->status == 'pending')

<form method="POST" action="{{ route('admin.izin.approve',$leave->id) }}">
@csrf
<button class="bg-green-600 text-white px-2 py-1 rounded text-sm">
Approve
</button>
</form>

<form method="POST" action="{{ route('admin.izin.reject',$leave->id) }}">
@csrf
<button class="bg-red-600 text-white px-2 py-1 rounded text-sm">
Reject
</button>
</form>

@endif

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</x-app-layout>
