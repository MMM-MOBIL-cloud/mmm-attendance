<x-app-layout>

<div class="p-6">

<h2 class="text-2xl font-bold mb-6">Approval Izin Kuliah</h2>

<table class="min-w-full bg-white shadow rounded-lg">

<thead class="bg-gray-100 text-center">
<tr>
<th class="p-3 w-1/6">User</th>
<th class="p-3 w-1/6">Tanggal</th>
<th class="p-3 w-1/5">Jam Kuliah</th>
<th class="p-3 w-1/5">Ganti Jam</th>
<th class="p-3 w-1/6">Status</th>
<th class="p-3 w-1/6">Action</th>
</tr>
</thead>

<tbody class="text-center">

@foreach($permissions as $p)

<tr class="border-t hover:bg-gray-50">

<td class="p-3 font-semibold">
{{ $p->user->name }}
</td>

<td class="p-3">
{{ \Carbon\Carbon::parse($p->date)->format('d M Y') }}
</td>

<td class="p-3">
{{ $p->start_time }} - {{ $p->end_time }}
</td>

<td class="p-3">

@if($p->replace_start)

{{ $p->replace_start }} - {{ $p->replace_end }}

@else

<span class="text-gray-400">Tidak ada</span>

@endif

</td>

<td class="p-3">

@if($p->status == 'pending')

<span class="bg-yellow-500 text-white px-3 py-1 rounded text-sm">
Pending
</span>

@elseif($p->status == 'approved')

<span class="bg-green-600 text-white px-3 py-1 rounded text-sm">
Approved
</span>

@else

<span class="bg-red-600 text-white px-3 py-1 rounded text-sm">
Rejected
</span>

@endif

</td>

<td class="p-3 flex justify-center gap-2">

@if($p->status == 'pending')

<form method="POST" action="{{ route('izin.kuliah.approve',$p->id) }}">
@csrf
<button class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
Approve
</button>
</form>

<form method="POST" action="{{ route('izin.kuliah.reject',$p->id) }}">
@csrf
<button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
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

</x-app-layout>
