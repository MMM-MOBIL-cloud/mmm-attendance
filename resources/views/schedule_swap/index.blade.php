<x-app-layout>

<div class="p-6">

<h1 class="text-2xl font-bold mb-4">
Request Tukar Jadwal Saya
</h1>

<a href="{{ route('swap.create') }}"
class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
Buat Request Tukar Jadwal
</a>

<table class="w-full mt-6 border border-gray-300">

<thead class="bg-gray-100 text-center">

<tr>

<th class="p-3 border">Dari Tanggal</th>
<th class="p-3 border">Menjadi Tanggal</th>
<th class="p-3 border">Tukar Dengan</th>
<th class="p-3 border">Status</th>

</tr>

</thead>

<tbody>

@foreach($requests as $req)

<tr class="text-center">

<td class="p-3 border">
{{ \Carbon\Carbon::parse($req->from_date)->format('d M Y') }}
</td>

<td class="p-3 border">
{{ \Carbon\Carbon::parse($req->to_date)->format('d M Y') }}
</td>

<td class="p-3 border">

@if($req->target_user_id)
{{ $req->targetUser->name ?? '-' }}
@else
Self Swap
@endif

</td>

<td class="p-3 border">

@if($req->status == 'Pending')
<span class="inline-block bg-yellow-500 text-white px-3 py-1 rounded-full text-sm">
Pending
</span>

@elseif($req->status == 'Approved')
<span class="inline-block bg-green-600 text-white px-3 py-1 rounded-full text-sm">
Approved
</span>

@elseif($req->status == 'Rejected')
<span class="inline-block bg-red-600 text-white px-3 py-1 rounded-full text-sm">
Rejected
</span>
@endif

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</x-app-layout>
