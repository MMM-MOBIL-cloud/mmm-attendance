<x-app-layout>

<div class="p-6">

<h1 class="text-2xl font-bold mb-6">
Edit User - {{ $user->name }}
</h1>

{{-- FORM UPDATE USER --}}
<form method="POST" action="{{ route('admin.user.update', $user->id) }}">
@csrf

<div class="mb-4">
<label class="block font-semibold mb-1">Nama</label>
<input type="text" name="name" value="{{ $user->name }}" class="border p-2 rounded w-full">
</div>

<div class="mb-4">
<label class="block font-semibold mb-1">Email</label>
<input type="email" name="email" value="{{ $user->email }}" class="border p-2 rounded w-full">
</div>

<div class="mb-4">
<label class="block font-semibold mb-1">Jabatan</label>

<select name="position" class="border p-2 rounded w-full">

<option value="Sales" {{ $user->position=='Sales'?'selected':'' }}>Sales</option>
<option value="Admin Office" {{ $user->position=='Admin Office'?'selected':'' }}>Admin Office</option>
<option value="Supervisor" {{ $user->position=='Supervisor'?'selected':'' }}>Supervisor</option>
<option value="Manager" {{ $user->position=='Manager'?'selected':'' }}>Manager</option>
<option value="Marketing" {{ $user->position=='Marketing'?'selected':'' }}>Marketing</option>
<option value="Host Live" {{ $user->position=='Host Live'?'selected':'' }}>Host Live</option>
<option value="Konten Kreator" {{ $user->position=='Konten Kreator'?'selected':'' }}>Konten Kreator</option>
<option value="Editor Konten" {{ $user->position=='Editor Konten'?'selected':'' }}>Editor Konten</option>
<option value="Videographer/Photographer" {{ $user->position=='Videographer/Photographer'?'selected':'' }}>Videographer/Photographer</option>
<option value="Office Boy / Cleaning Service" {{ $user->position=='Office Boy / Cleaning Service'?'selected':'' }}>Office Boy / Cleaning Service</option>
<option value="Other" {{ $user->position=='Other'?'selected':'' }}>Other</option>

</select>
</div>

<div class="mb-6">

<label class="block font-semibold mb-2">Akses Fitur</label>

<div class="flex items-center mb-2">
<input type="checkbox" name="can_swap_schedule" value="1"
{{ $user->can_swap_schedule ? 'checked' : '' }}
class="mr-2">
<span>Bisa Tukar Jadwal</span>
</div>

<div class="flex items-center mb-2">
<input type="checkbox" name="can_approve_swap" value="1"
{{ $user->can_approve_swap ? 'checked' : '' }}
class="mr-2">
<span>Bisa Approval Tukar Jadwal</span>
</div>

<div class="flex items-center mb-2">
<input type="checkbox" name="can_student_leave" value="1"
{{ $user->can_student_leave ? 'checked' : '' }}
class="mr-2">
<span>Bisa Ajukan Izin Kuliah</span>
</div>

<div class="flex items-center mb-2">
<input type="checkbox" name="can_general_leave" value="1"
{{ $user->can_general_leave ? 'checked' : '' }}
class="mr-2">
<span>Bisa Ajukan Izin (Tidak dihitung jam kerja)</span>
</div>

</div>

<button class="bg-blue-600 text-white px-4 py-2 rounded">
Update User
</button>

</form>

{{-- FORM DELETE USER --}}
<form method="POST"
action="{{ route('admin.user.delete',$user->id) }}"
class="mt-6"
onsubmit="return confirm('Yakin ingin menghapus user ini?')">

@csrf
@method('DELETE')

<button class="bg-red-600 text-white px-4 py-2 rounded">
Hapus User
</button>

</form>

</div>

</x-app-layout>
