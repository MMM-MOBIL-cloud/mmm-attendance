<x-app-layout>
<div class="p-6">

<h1 class="text-2xl font-bold mb-6">Manajemen User</h1>

<form method="POST" action="{{ route('admin.users.store') }}" class="bg-white p-6 rounded shadow mb-8">
@csrf

<div class="grid grid-cols-2 gap-4">

<div>
<label class="text-sm text-gray-600">Nama</label>
<input type="text" name="name" placeholder="Nama"
class="border p-2 rounded w-full" required>
</div>

<div>
<label class="text-sm text-gray-600">Email</label>
<input type="email" name="email" placeholder="Email"
class="border p-2 rounded w-full" required>
</div>

<div>
<label class="text-sm text-gray-600">Password</label>
<input type="password" name="password" placeholder="Password"
class="border p-2 rounded w-full" required>
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
<option value="Sales">Sales</option>
<option value="Admin Office">Admin Office</option>
<option value="Supervisor">Supervisor</option>
<option value="Manager">Manager</option>
<option value="Marketing">Marketing</option>
<option value="Host Live">Host Live</option>
<option value="Konten Kreator">Konten Kreator</option>
<option value="Editor Konten">Editor Konten</option>
<option value="Videographer/Photographer">Videographer/Photographer</option>
<option value="Office Boy / Cleaning Service">Office Boy / Cleaning Service</option>
<option value="Other">Other</option>
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
    <div class="col-span-2 mt-2">
<label class="font-semibold">Jadwal Hari Kerja</label>

<div class="grid grid-cols-4 gap-2 mt-2 text-sm">

<label><input type="checkbox" name="work_days[]" value="Monday"> Senin</label>
<label><input type="checkbox" name="work_days[]" value="Tuesday"> Selasa</label>
<label><input type="checkbox" name="work_days[]" value="Wednesday"> Rabu</label>
<label><input type="checkbox" name="work_days[]" value="Thursday"> Kamis</label>
<label><input type="checkbox" name="work_days[]" value="Friday"> Jumat</label>
<label><input type="checkbox" name="work_days[]" value="Saturday"> Sabtu</label>
<label><input type="checkbox" name="work_days[]" value="Sunday"> Minggu</label>

</div>
</div>


<button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
Tambah User
</button>

</form>
<form action="{{ route('admin.export.user') }}" method="GET" class="bg-white p-4 rounded shadow mb-6 flex gap-3 items-center">

<select name="user_id" class="border p-2 rounded" required>
<option value="">Pilih User</option>

@foreach($users as $user)
<option value="{{ $user->id }}">
{{ $user->name }}
</option>
@endforeach

</select>

<select name="month" class="border p-2 rounded" required>
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

<select name="year" class="border p-2 rounded">
<option value="{{ now()->year }}">{{ now()->year }}</option>
<option value="{{ now()->year - 1 }}">{{ now()->year - 1 }}</option>
</select>

<button class="bg-green-600 text-white px-4 py-2 rounded">
Export Absensi
</button>

</form>
<h2 class="text-xl font-bold mt-8 mb-4">Daftar User</h2>

<table class="min-w-full bg-white shadow rounded">

<thead>
<tr class="bg-gray-100">
<th class="p-2">Nama</th>
<th class="p-2">Email</th>
<th class="p-2">Role</th>
<th class="p-2">Jabatan</th>
<th class="p-2">Action</th>
</tr>
</thead>

<tbody>

@foreach($users as $user)

<tr class="border-t">
<td class="p-2">{{ $user->name }}</td>
<td class="p-2">{{ $user->email }}</td>
<td class="p-2">{{ $user->role }}</td>
<td class="p-2">

{{ $user->position ?? '-' }}

<a href="{{ route('admin.user.edit', $user->id) }}"
class="ml-2 text-gray-600 hover:text-blue-600">
⚙️
</a>

</td>
<td class="p-2">
<a href="{{ route('admin.user.schedule', $user->id) }}"
   class="bg-yellow-500 text-white px-3 py-1 rounded">
   Atur Jadwal
</a>
</td>
</tr>

@endforeach

</tbody>

</table>
</div>
</x-app-layout>
