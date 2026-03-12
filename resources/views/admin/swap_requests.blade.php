<x-app-layout>

<div class="p-6">

<h1 class="text-2xl font-bold mb-6">
Approval Tukar Jadwal
</h1>

<div class="overflow-x-auto">

<table class="min-w-full bg-white shadow rounded">

<thead class="bg-gray-100">

<tr class="text-center">

<th class="p-3 border">User</th>
<th class="p-3 border">Dari</th>
<th class="p-3 border">Menjadi</th>
<th class="p-3 border">Tukar Dengan</th>
<th class="p-3 border">Status</th>
<th class="p-3 border">Action</th>

</tr>

</thead>

<tbody>

@foreach($requests as $req)

<tr class="text-center border-b">

<td class="p-3 border">
{{ $req->requester->name ?? '-' }}
</td>

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

Self

@endif

</td>

<td class="p-3 border">

@if($req->status == 'Pending')

<span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-sm">
Pending
</span>

@elseif($req->status == 'Approved')

<span class="bg-green-600 text-white px-3 py-1 rounded-full text-sm">
Approved
</span>

@else

<span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm">
Rejected
</span>

@endif

</td>

<td class="p-3 border">

@if($req->status == 'Pending')

<form action="{{ route('admin.swap.approve',$req->id) }}" method="POST" class="inline">
@csrf
<button class="bg-green-600 text-white px-3 py-1 rounded">
Approve
</button>
</form>

<form action="{{ route('admin.swap.reject',$req->id) }}" method="POST" class="inline">
@csrf
<button class="bg-red-600 text-white px-3 py-1 rounded">
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
