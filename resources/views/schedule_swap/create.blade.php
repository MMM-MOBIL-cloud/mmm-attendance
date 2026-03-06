<x-app-layout>

<div class="p-6 max-w-xl">

<h1 class="text-2xl font-bold mb-4">Request Tukar Jadwal</h1>

@if(session('success'))
<div class="text-green-600 mb-4">
{{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('swap.store') }}">

@csrf

<div class="mb-4">
<label class="block mb-1">Jenis Tukar</label>

<select name="type" class="border rounded px-3 py-2 w-full">

<option value="self">Tukar Hari Sendiri</option>

<option value="swap">Tukar Dengan Karyawan</option>

</select>
</div>


<div class="mb-4">

<div class="mb-4">

<label class="block mb-1">Dari Tanggal</label>

<input type="date"
name="from_date"
class="border rounded px-3 py-2 w-full"
required>

</div>


<div class="mb-4">

<label class="block mb-1">Menjadi Tanggal</label>

<input type="date"
name="to_date"
class="border rounded px-3 py-2 w-full"
required>

</div>

</div>


<div class="mb-4">

<label class="block mb-1">Tukar Dengan</label>

<select name="target_user_id" class="border rounded px-3 py-2 w-full">

<option value="">-- Tidak Ada (Self Swap) --</option>

@foreach($users as $user)

<option value="{{ $user->id }}">
{{ $user->name }}
</option>

@endforeach

</select>

</div>


<button class="bg-blue-600 text-white px-4 py-2 rounded">

Kirim Request

</button>

</form>

</div>

</x-app-layout>
