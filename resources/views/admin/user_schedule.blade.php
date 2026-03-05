<x-app-layout>

<div class="p-6">

<h1 class="text-2xl font-bold mb-6">
Atur Jadwal Kerja - {{ $user->name }}
</h1>

<form method="POST" action="{{ route('admin.user.schedule.save', $user->id) }}">
@csrf

<h3 class="mb-3 font-semibold">Jadwal Hari Kerja</h3>

<label>
<input type="checkbox" name="days[]" value="Monday" {{ in_array('Monday',$workDays) ? 'checked' : '' }}>
Senin
</label><br>

<label>
<input type="checkbox" name="days[]" value="Tuesday" {{ in_array('Tuesday',$workDays) ? 'checked' : '' }}>
Selasa
</label><br>

<label>
<input type="checkbox" name="days[]" value="Wednesday" {{ in_array('Wednesday',$workDays) ? 'checked' : '' }}>
Rabu
</label><br>

<label>
<input type="checkbox" name="days[]" value="Thursday" {{ in_array('Thursday',$workDays) ? 'checked' : '' }}>
Kamis
</label><br>

<label>
<input type="checkbox" name="days[]" value="Friday" {{ in_array('Friday',$workDays) ? 'checked' : '' }}>
Jumat
</label><br>

<label>
<input type="checkbox" name="days[]" value="Saturday" {{ in_array('Saturday',$workDays) ? 'checked' : '' }}>
Sabtu
</label><br>

<label>
<input type="checkbox" name="days[]" value="Sunday" {{ in_array('Sunday',$workDays) ? 'checked' : '' }}>
Minggu
</label><br><br>

<button class="bg-blue-600 text-white px-4 py-2 rounded">
Simpan Jadwal
</button>

</form>

</div>

</x-app-layout>
