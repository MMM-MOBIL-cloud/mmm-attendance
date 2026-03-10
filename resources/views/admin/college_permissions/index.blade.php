<x-app-layout>

<div class="p-6">

<h2 class="text-2xl font-bold mb-6">Approval Izin Kuliah</h2>

<form method="GET" class="mb-4 flex gap-2">

<select name="month" class="border rounded px-3 py-2">
<option value="">Semua Bulan</option>
<option value="1">Januari</option>
<option value="2">Februari</option>
<option value="3">Maret</option>
<option value="4">April</option>
<option value="5">Mei</option>
<option value="6">Juni</option>
<option value="7">Juli</option>
<option value="8">Agustus</option>
<option value="9">September</option>
<option value="10">Oktober</option>
<option value="11">November</option>
<option value="12">Desember</option>
</select>

<select name="year" class="border rounded px-3 py-2">
<option value="">Semua Tahun</option>
<option value="2026">2026</option>
<option value="2025">2025</option>
<option value="2024">2024</option>
</select>

<button class="bg-blue-600 text-white px-4 py-2 rounded">
Filter
</button>

<a href="{{ url()->current() }}" class="bg-gray-500 text-white px-4 py-2 rounded">
Reset
</a>

</form>
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
