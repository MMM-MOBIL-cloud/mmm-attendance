<x-app-layout>
<div class="p-4 sm:p-6 max-w-7xl mx-auto">

<h1 class="text-2xl font-bold mb-6">Manajemen User</h1>

<!-- CARD FORM -->
<div class="bg-white rounded-2xl shadow p-6 mb-8">

<form method="POST" action="{{ route('admin.users.store') }}">
@csrf

<div class="grid grid-cols-2 gap-4">

<div>
<label class="text-sm text-gray-600">Nama</label>
<input type="text" name="name"
class="border p-2 rounded w-full focus:ring focus:ring-blue-200" required>
</div>

<div>
<label class="text-sm text-gray-600">Email</label>
<input type="email" name="email"
class="border p-2 rounded w-full focus:ring focus:ring-blue-200" required>
</div>

<div>
<label class="text-sm text-gray-600">Password</label>
<input type="password" name="password"
class="border p-2 rounded w-full focus:ring focus:ring-blue-200" required>
</div>

<div>
<label class="text-sm text-gray-600">Role</label>
<select name="role" class="border p-2 rounded w-full">
<option value="user">User</option>
<option value="admin">Admin</option>
</select>
</div>

<div>
<label class="text-sm text-gray-600">Jabatan</label>
<select name="position" class="border p-2 rounded w-full">
<option value="">Pilih Jabatan</option>
<option>Sales</option>
<option>Admin Office</option>
<option>Supervisor</option>
<option>Manager</option>
<option>Marketing</option>
<option>Host Live</option>
<option>Konten Kreator</option>
<option>Editor Konten</option>
<option>Videographer/Photographer</option>
<option>Office Boy / Cleaning Service</option>
<option>Other</option>
</select>
</div>

<div>
<label class="text-sm text-gray-600">Work Group</label>
<select name="work_group" class="border p-2 rounded w-full">
<option value="">Pilih Work Group</option>
<option value="office">Office</option>
<option value="sales">Sales</option>
</select>
</div>

<div>
<label class="text-sm text-gray-600">Jam Masuk</label>
<input type="time" name="shift_start"
class="border p-2 rounded w-full">
</div>

<div>
<label class="text-sm text-gray-600">Jam Pulang</label>
<input type="time" name="shift_end"
class="border p-2 rounded w-full">
</div>

</div>

<!-- JADWAL -->
<div class="mt-4">
<label class="font-semibold block mb-2">Jadwal Hari Kerja</label>

<div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm">

<div class="space-y-2">
<label class="flex items-center gap-2">
<input type="checkbox" name="work_days[]" value="Monday"> Senin
</label>

<label class="flex items-center gap-2">
<input type="checkbox" name="work_days[]" value="Tuesday"> Selasa
</label>

<label class="flex items-center gap-2">
<input type="checkbox" name="work_days[]" value="Wednesday"> Rabu
</label>

<label class="flex items-center gap-2">
<input type="checkbox" name="work_days[]" value="Thursday"> Kamis
</label>
</div>

<div class="space-y-2">
<label class="flex items-center gap-2">
<input type="checkbox" name="work_days[]" value="Friday"> Jumat
</label>

<label class="flex items-center gap-2">
<input type="checkbox" name="work_days[]" value="Saturday"> Sabtu
</label>

<label class="flex items-center gap-2">
<input type="checkbox" name="work_days[]" value="Sunday"> Minggu
</label>
</div>

</div>
</div>

<button class="mt-6 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
Tambah User
</button>

</form>
</div>

<!-- CARD EXPORT -->
<div class="bg-white p-4 rounded-2xl shadow mb-6 overflow-x-auto">

<form action="{{ route('admin.export.user') }}" method="GET"
class="flex flex-col md:flex-row gap-3 md:items-center">

<select name="user_id" class="border p-2 rounded w-full md:w-auto" required>
<option value="">Pilih User</option>
@foreach($users as $user)
<option value="{{ $user->id }}">{{ $user->name }}</option>
@endforeach
</select>

<select name="month" class="border p-2 rounded w-full md:w-auto" required>
<option value="">Pilih Bulan</option>
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

<select name="year" class="border p-2 rounded w-full md:w-auto">
<option>{{ now()->year }}</option>
<option>{{ now()->year - 1 }}</option>
</select>

<button class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg">
Export Absensi
</button>

</form>
</div>

<!-- TABLE USER -->
<h2 class="text-xl font-bold mb-3">Daftar User</h2>

<div class="bg-white shadow rounded-2xl overflow-hidden">

<div class="overflow-x-auto">
<table class="min-w-[900px] w-full">

<thead class="bg-gray-100 text-sm">
<tr class="text-center">
<th class="p-3">Nama</th>
<th class="p-3">Email</th>
<th class="p-3">Role</th>
<th class="p-3">Jabatan</th>
<th class="p-3">Work Group</th>
<th class="p-3">Status</th>
<th class="p-3">Action</th>
</tr>
</thead>

<tbody>

@foreach($users as $user)

<tr class="border-t text-center hover:bg-gray-50 cursor-pointer"
onclick="window.location='{{ route('admin.user.edit', $user->id) }}'">

<td class="p-3 text-blue-600 font-semibold">
{{ $user->name }}
</td>

<td class="p-3">
{{ $user->email }}
</td>

<td class="p-3 capitalize">
{{ $user->role }}
</td>

<td class="p-3">
{{ $user->position ?? '-' }}
</td>

<td class="p-3 capitalize">
{{ $user->work_group ?? '-' }}
</td>

<td>
<form action="{{ route('admin.users.toggleStatus', $user->id) }}" method="POST">
@csrf
@method('PATCH')

<button type="submit"
class="px-3 py-1 rounded text-white
{{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}">
    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
</button>

</form>
</td>

<td class="p-3">
<a href="{{ route('admin.user.schedule', $user->id) }}"
class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
Atur Jadwal
</a>
</td>

</tr>

@endforeach

</tbody>

</table>
</div>

</div>

</div>
</x-app-layout>
