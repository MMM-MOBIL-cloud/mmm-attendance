<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;
use App\Models\Attendance;
use App\Exports\AttendanceExport;
use App\Exports\MonthlyAttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ScheduleSwapController;
use App\Http\Controllers\CollegePermissionController;
use App\Http\Controllers\GeneralLeaveController;

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
Route::get('/dashboard', [UserDashboardController::class,'index'])
    ->middleware(['auth','verified'])
    ->name('dashboard');

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

    Route::get('/izin-kuliah', [CollegePermissionController::class,'create'])->name('izin.kuliah');

    Route::post('/izin-kuliah/store', [CollegePermissionController::class,'store'])->name('izin.kuliah.store');

    Route::get('/izin', [GeneralLeaveController::class, 'index'])->name('izin.index');

    Route::get('/izin/create', [GeneralLeaveController::class, 'create'])->name('izin.create');

    Route::post('/izin/store', [GeneralLeaveController::class, 'store'])->name('izin.store');

});

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class,'dashboard'])
    ->name('admin.dashboard');

    Route::get('/admin/jadwal-piket-hari-ini',
    [AdminController::class,'jadwalPiketHariIni']
    )->name('admin.jadwal.piket.hari.ini');

    // ========================
    // ADMIN IZIN KULIAH
    // ========================

    Route::get('/admin/izin-kuliah', [CollegePermissionController::class,'index'])
        ->name('izin.kuliah.admin');

    Route::post('/admin/izin-kuliah/{id}/approve', [CollegePermissionController::class,'approve'])
        ->name('izin.kuliah.approve');

    Route::post('/admin/izin-kuliah/{id}/reject', [CollegePermissionController::class,'reject'])
        ->name('izin.kuliah.reject');

    // ========================
// ADMIN IZIN UMUM
// ========================

Route::get('/admin/izin', [GeneralLeaveController::class, 'adminIndex'])
    ->name('admin.izin.index');

Route::post('/admin/izin/{id}/approve', [GeneralLeaveController::class, 'approve'])
    ->name('admin.izin.approve');

Route::post('/admin/izin/{id}/reject', [GeneralLeaveController::class, 'reject'])
    ->name('admin.izin.reject');

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

});

require __DIR__.'/auth.php';
