<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

    public function users()
    {
        $users = User::where('role','user')->get();

        return view('admin.users', compact('users'));
    }

    public function attendanceToday(Request $request)
{

    $query = Attendance::with('user');

    // ⭐ DEFAULT tampilkan hari ini
    if(!$request->date && !$request->month && !$request->year){
        $query->whereDate('date', now()->toDateString());
    }

    // filter user
    if($request->user_id){
        $query->where('user_id',$request->user_id);
    }

    // filter tanggal
    if($request->date){
        $query->whereDate('date',$request->date);
    }

    // filter bulan
    if($request->month){
        $query->whereMonth('date',$request->month);
    }

    // filter tahun
    if($request->year){
        $query->whereYear('date',$request->year);
    }

    $attendances = $query->latest()->paginate(20);

    return view('admin.attendance_today', compact('attendances'));
}

    public function storeUser(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:4',
        'role' => 'required'
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'position' => $request->position,
        'work_group' => $request->work_group,
        'shift_start' => $request->shift_start,
        'shift_end' => $request->shift_end,
    ]);

    if($request->work_days){
        foreach($request->work_days as $day){
            DB::table('user_work_days')->insert([
                'user_id' => $user->id,
                'day' => $day,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    return redirect()->back()->with('success', 'User berhasil dibuat');
}

public function userSchedule($id)
{
    $user = User::findOrFail($id);

    $workDays = \DB::table('user_work_days')
        ->where('user_id', $id)
        ->pluck('day')
        ->toArray();

    return view('admin.user_schedule', compact('user','workDays'));
}

public function saveUserSchedule(Request $request, $id)
{
    // hapus jadwal lama
    \DB::table('user_work_days')
        ->where('user_id', $id)
        ->delete();

    // simpan jadwal baru
    if ($request->days) {

        foreach ($request->days as $day) {

            \DB::table('user_work_days')->insert([
                'user_id' => $id,
                'day' => $day,
                'created_at' => now(),
                'updated_at' => now()
            ]);

        }

    }

    return redirect()->back()->with('success','Jadwal kerja berhasil disimpan');
}

public function editUser($id)
{
    $user = User::findOrFail($id);

    return view('admin.user_edit', compact('user'));
}

public function updateUser(Request $request, $id)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'nullable|min:6'
    ]);

    $user = User::findOrFail($id);

    // ❗ tidak boleh nonaktifkan diri sendiri
if (auth()->id() == $user->id && $request->is_active == 0) {
    return back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri');
}

// ❗ tidak boleh nonaktifkan super admin
if ($user->role === 'super_admin' && $request->is_active == 0) {
    return back()->with('error', 'Super Admin tidak bisa dinonaktifkan');
}

// ❗ harus ada minimal 1 admin aktif
if ($request->is_active == 0 && $user->role === 'admin') {

    $activeAdmin = \App\Models\User::where('role','admin')
    ->where('is_active',1)
    ->where('id','!=',$user->id) // ⭐ penting
    ->count();

    if ($activeAdmin <= 1) {
        return back()->with('error', 'Minimal harus ada 1 admin aktif');
    }
}

    $user->name = $request->name;
    $user->email = $request->email;
    $user->position = $request->position;
    $user->work_group = $request->work_group;
    $user->is_active = $request->is_active;

    $user->can_swap_schedule = $request->has('can_swap_schedule');
    $user->can_approve_swap = $request->has('can_approve_swap');
    $user->can_student_leave = $request->has('can_student_leave');
    $user->can_general_leave = $request->has('can_general_leave');

    // ⭐ INI YANG PALING PENTING
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return redirect()->route('admin.users')
        ->with('success', 'Data user berhasil diupdate');
}

public function toggleStatus($id)
{
    $user = \App\Models\User::findOrFail($id);

    // ❗ tidak boleh ubah status akun sendiri
    if (auth()->id() == $user->id) {
        return back()->with('error','Tidak bisa ubah status akun sendiri');
    }

    // ❗ super admin tidak boleh diubah
    if ($user->role === 'super_admin') {
        return back()->with('error','Status Super Admin tidak bisa diubah');
    }

    // ❗ minimal harus ada 1 admin aktif
    if ($user->role === 'admin' && $user->is_active) {

        $activeAdmin = \App\Models\User::where('role','admin')
            ->where('is_active',1)
            ->where('id','!=',$user->id)
            ->count();

        if ($activeAdmin < 1) {
            return back()->with('error','Minimal harus ada 1 admin aktif');
        }
    }

    // ⭐ TOGGLE STATUS
    $user->is_active = !$user->is_active;
    $user->save();

    return back()->with('success','Status user berhasil diubah');
}

public function deleteUser($id)
{
    $user = User::findOrFail($id);

    $user->delete();

    return redirect()->route('admin.users')
        ->with('success','User berhasil dihapus');
}

public function dashboard(Request $request)
{
    $query = Attendance::with('user');

    if ($request->filled('date')) {
        $query->where('date', $request->date);
    }

    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    $attendances = $query->latest()->paginate(10);

    $users = User::all();

    // Statistik
    $totalUsers = User::count();
    $totalAbsensi = Attendance::count();

    $today = now()->toDateString();

    $hadirHariIni = Attendance::whereDate('date', $today)
        ->whereNotNull('check_in')
        ->distinct('user_id')
        ->count('user_id');

    // Grafik bulanan
    $grafikRaw = Attendance::select(
        DB::raw('MONTH(date) as bulan'),
        DB::raw('COUNT(*) as total')
    )
        ->whereYear('date', now()->year)
        ->groupBy('bulan')
        ->pluck('total', 'bulan');

    $grafikBulanan = [];

    for ($i = 1; $i <= 12; $i++) {
        $grafikBulanan[$i] = $grafikRaw[$i] ?? 0;
    }

    // Statistik bulan ini
    $currentMonth = now()->month;
    $currentYear = now()->year;

    $totalHadirBulanIni = Attendance::whereMonth('date', $currentMonth)
        ->whereYear('date', $currentYear)
        ->whereNotNull('check_in')
        ->count();

    $totalTerlambatBulanIni = Attendance::whereMonth('date', $currentMonth)
        ->whereYear('date', $currentYear)
        ->where('status', 'like', '%Terlambat%')
        ->count();

    $totalBelumPulangHariIni = Attendance::whereDate('date', now())
        ->whereNull('check_out')
        ->count();

    $totalPulangCepat = Attendance::whereMonth('date', $currentMonth)
        ->whereYear('date', $currentYear)
        ->where('status', 'like', '%Pulang Cepat%')
        ->count();

    /// ===============================
    // JADWAL PIKET HARI INI
    // ===============================

    $todayName = now()->format('l'); // contoh: Monday, Tuesday

    $jadwalPiketHariIni = DB::table('user_work_days')
    ->join('users', 'user_work_days.user_id', '=', 'users.id')
    ->whereRaw('LOWER(user_work_days.day) = ?', [strtolower($todayName)])
    ->select('users.id', 'users.name')
    ->orderBy('users.name')
    ->get();

    $totalJadwalPiketHariIni = $jadwalPiketHariIni->count();

    // Ranking hadir
    $rankingHadir = User::withCount(['attendances as total_hadir' => function ($q) {
        $q->whereMonth('date', now()->month)
          ->whereYear('date', now()->year)
          ->whereNotNull('check_in');
    }])
        ->orderByDesc('total_hadir')
        ->take(5)
        ->get();

    // Ranking terlambat
    $rankingTerlambat = User::withCount(['attendances as total_terlambat' => function ($q) {
        $q->whereMonth('date', now()->month)
          ->whereYear('date', now()->year)
          ->where('status', 'like', '%Terlambat%');
    }])
        ->orderByDesc('total_terlambat')
        ->take(5)
        ->get();

    // Ranking jam kerja
    $rankingJamKerja = DB::table('attendances')
        ->join('users', 'attendances.user_id', '=', 'users.id')
        ->select(
            'users.name',
            'users.id',
            DB::raw('COUNT(attendances.id) as total_hadir'),
            DB::raw('COALESCE(SUM(attendances.work_hours),0) as total_jam')
        )
        ->whereMonth('attendances.date', now()->month)
        ->whereYear('attendances.date', now()->year)
        ->groupBy('users.id', 'users.name')
        ->orderByDesc('total_hadir')
        ->orderByDesc('total_jam')
        ->limit(5)
        ->get();

    $rankingSales = DB::table('attendances')
    ->join('users', 'attendances.user_id','=','users.id')

    ->where('users.is_active', 1)
    ->where('attendances.work_group','sales')   // ⭐ WAJIB INI
    ->whereMonth('attendances.date', now()->month)
    ->whereYear('attendances.date', now()->year)

    ->select(
        'users.name',
        'users.id',
        DB::raw('COUNT(attendances.id) as total_hadir'),
        DB::raw('COALESCE(SUM(attendances.work_hours),0) as total_jam')
    )
    ->groupBy('users.id','users.name')
    ->orderByDesc('total_hadir')
    ->orderByDesc('total_jam')
    ->limit(5)
    ->get();

$rankingOffice = DB::table('attendances')
    ->join('users', 'attendances.user_id','=','users.id')

    ->where('users.is_active', 1)
    ->where('attendances.work_group','office')   // ⭐ WAJIB
    ->whereMonth('attendances.date', now()->month)
    ->whereYear('attendances.date', now()->year)

    ->select(
        'users.name',
        'users.id',
        DB::raw('COUNT(attendances.id) as total_hadir'),
        DB::raw('COALESCE(SUM(attendances.work_hours),0) as total_jam')
    )
    ->groupBy('users.id','users.name')
    ->orderByDesc('total_hadir')
    ->orderByDesc('total_jam')
    ->limit(5)
    ->get();

$rankingOfficeLate = Attendance::whereMonth('date', now()->month)
    ->whereYear('date', now()->year)
    ->where('status', 'like', '%Terlambat%')
    ->whereHas('user', function ($q) {
        $q->where('work_group', 'office')
        ->where('is_active', 1);
    })
    ->get()
    ->groupBy('user_id')
    ->map(function ($items) {

        $totalDays = $items->count();

        $totalLateMinutes = $items->sum(function ($att) {
            return $att->late_minutes;
        });

        return [
            'user' => $items->first()->user,
            'days' => $totalDays,
            'hours' => floor($totalLateMinutes / 60),
            'minutes' => $totalLateMinutes % 60,
        ];
    })
    ->sortByDesc(function ($item) {
    return $item['days'] * 10000 + ($item['hours'] * 60 + $item['minutes']);
})
    ->values()
    ->take(5);

$rankingSalesLate = Attendance::whereMonth('date', now()->month)
    ->whereYear('date', now()->year)
    ->where('status', 'like', '%Terlambat%')
    ->whereHas('user', function ($q) {
        $q->where('work_group', 'sales')
        ->where('is_active', 1);
    })
    ->get()
    ->groupBy('user_id')
    ->map(function ($items) {

        $totalDays = $items->count();

        $totalLateMinutes = $items->sum(function ($att) {
            return $att->late_minutes;
        });

        return [
            'user' => $items->first()->user,
            'days' => $totalDays,
            'hours' => floor($totalLateMinutes / 60),
            'minutes' => $totalLateMinutes % 60,
        ];
    })
    ->sortByDesc(function ($item) {
    return $item['days'] * 10000 + ($item['hours'] * 60 + $item['minutes']);
})
    ->values()
    ->take(5);

    return view('admin.dashboard', compact(
        'attendances',
        'users',
        'totalUsers',
        'totalAbsensi',
        'hadirHariIni',
        'grafikBulanan',
        'totalHadirBulanIni',
        'totalTerlambatBulanIni',
        'totalBelumPulangHariIni',
        'rankingHadir',
        'rankingTerlambat',
        'rankingJamKerja',
        'rankingSales',
        'rankingOffice',
        'rankingOfficeLate',
        'rankingSalesLate',
        'jadwalPiketHariIni',
        'totalJadwalPiketHariIni',
        'totalPulangCepat'
    ));
}

public function jadwalPiketHariIni()
{
    $todayName = now()->format('l');
    $todayDate = now()->format('Y-m-d');

    $users = \DB::table('user_work_days')
        ->join('users', 'user_work_days.user_id', '=', 'users.id')
        ->leftJoin('attendances', function ($join) use ($todayDate) {
            $join->on('users.id', '=', 'attendances.user_id')
                 ->where('attendances.date', $todayDate);
        })
        ->whereRaw('LOWER(user_work_days.day) = ?', [strtolower($todayName)])
        ->select(
            'users.*',
            'attendances.check_in',
            'attendances.check_out'
        )
        ->orderBy('users.name')
        ->get();

    return view('admin.jadwal_piket_hari_ini', compact('users'));
}

}
