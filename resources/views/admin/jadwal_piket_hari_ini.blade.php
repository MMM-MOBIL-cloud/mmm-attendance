<x-app-layout>

<div class="p-6">

<h1 class="text-2xl font-bold mb-6">
Jadwal Piket Hari Ini
</h1>

<div class="bg-white rounded-xl shadow overflow-hidden">

@forelse($users as $user)

<div class="flex items-center justify-between border-b p-4">

<div class="flex items-center gap-4">

<img
src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}"
class="w-12 h-12 rounded-full object-cover">

<div>
<div class="font-semibold text-lg">
{{ $user->name }}
</div>

<div class="text-sm text-gray-500">
{{ ucfirst($user->work_group) }}
</div>
</div>

</div>

<div>

@php
$terlambat = false;

if($user->check_in){
    $batas = \Carbon\Carbon::parse(config('app.work_time.start'))
        ->addMinutes(config('app.work_time.tolerance_minutes'));

    $jamMasuk = \Carbon\Carbon::parse($user->check_in);

    if($jamMasuk->gt($batas)){
        $terlambat = true;
    }
}
@endphp

@if($user->check_in)

<div class="text-right">

@if($terlambat)
<span class="bg-yellow-500 text-white px-4 py-1 rounded-full text-sm">
Terlambat
</span>
@else
<span class="bg-green-500 text-white px-4 py-1 rounded-full text-sm">
Hadir
</span>
@endif

    <div class="text-xs text-gray-500 mt-1">
        Jam Masuk:
        {{ \Carbon\Carbon::parse($user->check_in)->format('H:i') }}
    </div>
</div>

@else

<span class="bg-red-500 text-white px-4 py-1 rounded-full text-sm">
Belum Hadir
</span>

@endif

</div>

</div>

@empty

<div class="p-6 text-gray-500 text-center">
Tidak ada jadwal piket hari ini
</div>

@endforelse

</div>

</div>

</x-app-layout>
