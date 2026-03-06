<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Models\Attendance;
use App\Exports\AttendanceExport;
use App\Exports\MonthlyAttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ScheduleSwapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🔹 Redirect root ke dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// 🔹 Dashboard
Route::get('/dashboard', function () {
    $today = now()->format('Y-m-d');

    $attendanceToday = Attendance::where('user_id', auth()->id())
        ->where('date', $today)
        ->first();

    $attendanceHistory = Attendance::where('user_id', auth()->id())
        ->orderBy('date', 'desc')
        ->take(7)
        ->get();

    // Rekap Bulanan
    $currentMonth = now()->month;
    $currentYear = now()->year;

    $totalHadirBulanIni = Attendance::where('user_id', auth()->id())
        ->whereMonth('date', $currentMonth)
        ->whereYear('date', $currentYear)
        ->whereNotNull('check_in')
        ->count();

    $totalTerlambatBulanIni = Attendance::where('user_id', auth()->id())
        ->whereMonth('date', $currentMonth)
        ->whereYear('date', $currentYear)
        ->where('status', 'Terlambat')
        ->count();

    // ======================
// HITUNG TOTAL MENIT TERLAMBAT BULAN INI
// ======================

$batasMasuk = \Carbon\Carbon::createFromTime(8, 15, 0);

$totalMenitTerlambat = 0;

$absensiTerlambat = Attendance::where('user_id', auth()->id())
    ->whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->where('status', 'Terlambat')
    ->get();

foreach ($absensiTerlambat as $absen) {
    $jamMasuk = \Carbon\Carbon::parse($absen->check_in);
    $selisih = $batasMasuk->diffInMinutes($jamMasuk, false);

    if ($selisih > 0) {
        $totalMenitTerlambat += $selisih;
    }
}

// Konversi ke jam & menit
$totalJamTerlambat = floor($totalMenitTerlambat / 60);
$sisaMenitTerlambat = $totalMenitTerlambat % 60;

    $totalBelumPulang = Attendance::where('user_id', auth()->id())
        ->whereMonth('date', $currentMonth)
        ->whereYear('date', $currentYear)
        ->whereNotNull('check_in')
        ->whereNull('check_out')
        ->count();

    return view('dashboard', [
        'attendanceToday' => $attendanceToday,
        'attendanceHistory' => $attendanceHistory,
        'totalHadirBulanIni' => $totalHadirBulanIni,
        'totalTerlambatBulanIni' => $totalTerlambatBulanIni,
        'totalJamTerlambat' => $totalJamTerlambat,
        'sisaMenitTerlambat' => $sisaMenitTerlambat,
        'totalBelumPulang' => $totalBelumPulang,
    ]);

})->middleware(['auth', 'verified'])->name('dashboard');


// 🔹 Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 🔹 Absensi
Route::middleware(['auth'])->group(function () {

    Route::post('/check-in', [AttendanceController::class, 'checkIn'])
        ->name('check.in');

    Route::post('/check-out', [AttendanceController::class, 'checkOut'])
        ->name('check.out');

    Route::get('/swap-schedule',
        [ScheduleSwapController::class, 'index'])
        ->name('swap.index');

    Route::get('/swap-schedule/create',
[   ScheduleSwapController::class,'create'])
        ->name('swap.create');

    Route::post('/swap-schedule/store',
    [ScheduleSwapController::class,'store'])
        ->name('swap.store');

    Route::get('/swap-approval',
    [App\Http\Controllers\SwapApprovalController::class,'index'])
    ->name('swap.approval.index');

    Route::post('/swap-approval/{id}/approve',
    [App\Http\Controllers\SwapApprovalController::class,'approve'])
    ->name('swap.approval.approve');

    Route::post('/swap-approval/{id}/reject',
    [App\Http\Controllers\SwapApprovalController::class,'reject'])
    ->name('swap.approval.reject');

});

Route::middleware(['auth', 'admin'])->group(function () {
    // =======================
// APPROVE / REJECT CHECKOUT
// =======================

    Route::post('/attendance/{id}/approve-checkout', [AttendanceController::class, 'approveCheckout'])
    ->name('attendance.approve.checkout');

    Route::post('/attendance/{id}/reject-checkout', [AttendanceController::class, 'rejectCheckout'])
    ->name('attendance.reject.checkout');

    Route::post('/attendance/{id}/approve', [AttendanceController::class, 'approve'])
    ->name('attendance.approve');

    Route::post('/attendance/{id}/reject', [AttendanceController::class, 'reject'])
    ->name('attendance.reject');
    Route::get('/admin/dashboard', function (\Illuminate\Http\Request $request) {

        $query = \App\Models\Attendance::with('user');

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
    Route::get('/admin/users', function () {
    $users = \App\Models\User::all();
    return view('admin.users', compact('users'));
})->name('admin.users');

Route::post('/admin/users', function (\Illuminate\Http\Request $request) {

    \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
    ]);

    return back()->with('success', 'User berhasil ditambahkan');

    Route::get('/admin/users', function () {
    $users = \App\Models\User::all();
    return view('admin.users', compact('users'));
})->name('admin.users');

Route::post('/admin/users', function (\Illuminate\Http\Request $request) {

    \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
        'shift_start' => $request->shift_start,
        'shift_end' => $request->shift_end,
    ]);

    return back()->with('success', 'User berhasil ditambahkan');
})->name('admin.users.store');

})->name('admin.users.store');

        // ✅ Pagination (bukan get())
        $attendances = $query->orderBy('date', 'desc')->paginate(10);

        $users = User::all();

        // Statistik
        $totalUsers = User::count();
        $totalAbsensi = \App\Models\Attendance::count();

        $today = now()->format('Y-m-d');

        $hadirHariIni = \App\Models\Attendance::where('date', $today)->count();

        // ✅ Grafik Bulanan
        $grafikRaw = \App\Models\Attendance::select(
        DB::raw('MONTH(date) as bulan'),
        DB::raw('COUNT(*) as total')
    )
    ->whereYear('date', now()->year)
    ->groupBy('bulan')
    ->pluck('total', 'bulan');

    // Statistik bulan ini
$currentMonth = now()->month;
$currentYear = now()->year;

$totalHadirBulanIni = Attendance::whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->whereNotNull('check_in')
    ->count();

$totalTerlambatBulanIni = Attendance::whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->whereTime('check_in', '>', '08:00:00')
    ->count();

$totalBelumPulangHariIni = Attendance::whereDate('date', now())
    ->whereNull('check_out')
    ->count();

// Buat array 12 bulan default 0
$grafikBulanan = [];

for ($i = 1; $i <= 12; $i++) {
    $grafikBulanan[$i] = $grafikRaw[$i] ?? 0;
}

// Ranking paling rajin (hadir terbanyak bulan ini)
$rankingHadir = \App\Models\User::withCount(['attendances as total_hadir' => function ($query) {
    $query->whereMonth('date', now()->month)
          ->whereYear('date', now()->year)
          ->whereNotNull('check_in');
}])
->orderByDesc('total_hadir')
->take(5)
->get();


// Ranking paling sering terlambat
$rankingTerlambat = \App\Models\User::withCount(['attendances as total_terlambat' => function ($query) {
    $query->whereMonth('date', now()->month)
          ->whereYear('date', now()->year)
          ->whereTime('check_in', '>', '08:00:00');
}])
->orderByDesc('total_terlambat')
->take(5)
->get();

$currentMonth = now()->month;
$currentYear = now()->year;

// Total hadir bulan ini
$totalHadirBulanIni = \App\Models\Attendance::whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->where('status', 'like', '%Hadir%')
    ->count();

// Total terlambat bulan ini
$totalTerlambatBulanIni = \App\Models\Attendance::whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->where('status', 'like', '%Terlambat%')
    ->count();

// Total pulang cepat
$totalPulangCepat = \App\Models\Attendance::whereMonth('date', $currentMonth)
    ->whereYear('date', $currentYear)
    ->where('status', 'like', '%Pulang Cepat%')
    ->count();

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
    'totalHadirBulanIni',
    'totalTerlambatBulanIni',
    'totalPulangCepat'
));

    })->name('admin.dashboard');


    Route::get('/admin/export', function () {
        return Excel::download(new AttendanceExport, 'absensi.xlsx');
    })->name('admin.export');

    Route::get('/admin/export-user', function () {

    return Excel::download(
        new AttendanceExport(
            request('user_id'),
            request('month'),
            request('year')
        ),
        'laporan-absensi-user.xlsx'
    );

})->name('admin.export.user');

Route::get('/admin/export/{month}/{year}', function ($month, $year) {
    return Excel::download(
        new MonthlyAttendanceExport($month, $year),
        "Laporan_Absensi_{$month}_{$year}.xlsx"
    );
})->name('admin.export.monthly');

// =======================
// ADMIN APPROVAL TUKAR JADWAL
// =======================

Route::get('/admin/swap-requests', [App\Http\Controllers\AdminSwapController::class, 'index'])
    ->name('admin.swap.index');

Route::post('/admin/swap-requests/{id}/approve', [App\Http\Controllers\AdminSwapController::class, 'approve'])
    ->name('admin.swap.approve');

Route::post('/admin/swap-requests/{id}/reject', [App\Http\Controllers\AdminSwapController::class, 'reject'])
    ->name('admin.swap.reject');


});
Route::post('/invite', [InvitationController::class, 'invite'])->name('invite');

Route::get('/invite-register/{token}', [InvitationController::class, 'showRegister']);
Route::post('/invite-register/{token}', [InvitationController::class, 'processRegister']);

Route::get('/admin/users', [App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');

Route::get('/admin/users/{id}/schedule', [AdminController::class, 'userSchedule'])
    ->name('admin.user.schedule');

Route::post('/admin/users/{id}/schedule', [AdminController::class, 'saveUserSchedule'])
    ->name('admin.user.schedule.save');

Route::post('/admin/users/store', [AdminController::class, 'storeUser'])->name('admin.users.store');

Route::get('/admin/attendance-today', [App\Http\Controllers\AdminController::class, 'attendanceToday'])->name('admin.today');

Route::get('/admin/users/{id}/edit', [AdminController::class, 'editUser'])
    ->name('admin.user.edit');

Route::post('/admin/users/{id}/update', [AdminController::class, 'updateUser'])
    ->name('admin.user.update');

Route::delete('/admin/users/{id}/delete', [AdminController::class, 'deleteUser'])
    ->name('admin.user.delete');

require __DIR__.'/auth.php';
