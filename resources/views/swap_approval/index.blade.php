<x-app-layout>

<div class="p-6">

<h1 class="text-2xl font-bold mb-4">
Approval Tukar Jadwal
</h1>

<table class="w-full border">

<thead class="bg-gray-100 text-center">

<tr>

<th class="p-3">User</th>
<th class="p-3">Dari</th>
<th class="p-3">Menjadi</th>
<th class="p-3">Action</th>

</tr>

</thead>

<tbody>

@foreach($requests as $req)

<tr class="border-b text-center">

<td class="p-2">
{{ $req->requester->name }}
</td>

<td class="p-2">
{{ \Carbon\Carbon::parse($req->from_date)->format('d M Y') }}
</td>

<td class="p-2">
{{ \Carbon\Carbon::parse($req->to_date)->format('d M Y') }}
</td>

<td class="p-2 flex justify-center gap-2">

<form method="POST"
action="{{ route('swap.approval.approve',$req->id) }}">
@csrf
<button class="bg-green-600 text-white px-3 py-1 rounded">
Approve
</button>
</form>

<form method="POST"
action="{{ route('swap.approval.reject',$req->id) }}">
@csrf
<button class="bg-red-600 text-white px-3 py-1 rounded">
Reject
</button>
</form>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</x-app-layout>
