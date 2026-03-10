<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\AttendanceRejectedNotification;
use Carbon\Carbon;
use App\Models\CollegePermission;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AttendanceController extends Controller
{
    public function approve($id)
{
    $attendance = Attendance::findOrFail($id);

    $attendance->update([
        'approval_status' => 'Approved'
    ]);

    return back()->with('success', 'Absensi berhasil disetujui.');
}

public function reject($id)
{
    $attendance = Attendance::findOrFail($id);

    $attendance->update([
        'approval_status' => 'Rejected'
    ]);

    // kirim notifikasi ke user
    $attendance->user->notify(
        new AttendanceRejectedNotification($attendance)
    );

    return back()->with('success', 'Absensi ditolak.');
}
public function approveCheckout($id)
{
    $attendance = Attendance::findOrFail($id);

    $attendance->update([
        'checkout_approval_status' => 'Approved'
    ]);

    return back()->with('success', 'Check-out disetujui.');
}

public function rejectCheckout($id)
{
    $attendance = Attendance::findOrFail($id);

    $attendance->update([
        'checkout_approval_status' => 'Rejected'
    ]);

    return back()->with('success', 'Check-out ditolak.');
}
    public function checkIn(Request $request)
{
    $today = now()->format('Y-m-d');
    $now = now();

    // =====================
    // CEK IZIN HARI INI
    // =====================
    $approvedLeave = \App\Models\GeneralLeave::where('user_id', auth()->id())
    ->where('date', $today)
    ->where('status', 'approved')
    ->first();

if ($approvedLeave) {

    $leaveStart = Carbon::parse($approvedLeave->start_time);
    $leaveEnd = Carbon::parse($approvedLeave->end_time);

    $workStart = Carbon::createFromTime(8,0,0);
    $workEnd = Carbon::createFromTime(16,0,0);

    // jika izin penuh jam kerja
    if ($leaveStart <= $workStart && $leaveEnd >= $workEnd) {
        return back()->with('error','Anda izin penuh hari ini, tidak perlu absensi.');
    }

}

    $todayName = now()->format('l');

$workDays = \DB::table('user_work_days')
    ->where('user_id', auth()->id())
    ->pluck('day')
    ->toArray();

if (!in_array($todayName, $workDays)) {
    return back()->with('error', 'Hari ini bukan jadwal kerja anda.');
}

    // ======================
// BATAS MULAI CHECK-IN 07:45
// ======================
$batasMulai = now()->setTime(7, 45, 0);

if ($now->lt($batasMulai)) {
    return back()->with('error', 'Check-in hanya bisa mulai jam 07:45.');
}

// ======================
// BATAS AKHIR CHECK-IN 11:00
// ======================
$batasAkhir = now()->setTime(11, 0, 0);

if ($now->gt($batasAkhir)) {
    return back()->with('error', 'Check-in maksimal sampai jam 11:00.');
}

    // ======================
    // WAJIB SELFIE
    // ======================
    if (!$request->photo) {
        return back()->with('error', 'Selfie wajib sebelum absen!');
    }

    // ======================
    // CEK SUDAH ABSEN
    // ======================
    $existing = Attendance::where('user_id', auth()->id())
    ->where('date', $today)
    ->first();

if ($existing && $existing->approval_status != 'Rejected') {
    return back()->with('error', 'Anda sudah check-in hari ini.');
}

// Kalau Rejected → hapus record lama dulu
if ($existing && $existing->approval_status == 'Rejected') {
    $existing->delete();
}

    // ======================
    // UPLOAD FOTO DULU
    // ======================
    $imageParts = explode(',', $request->photo);

if (count($imageParts) == 2) {

    $imageBase64 = $imageParts[1];

    // Folder berdasarkan bulan
    $folder = 'attendance/' . Carbon::now()->format('Y-m');

    // Nama file unik
    $imageName = 'absen_' . time() . '_' . auth()->id() . '.png';

    // Path lengkap
    $photoPath = $folder . '/' . $imageName;

    $manager = new ImageManager(new Driver());

    $image = $manager->read($imageBase64);

    $text = "MMM MOBIL\n"
        . now()->format('d M Y H:i:s') . "\n"
        . auth()->user()->name . "\n"
        . "Lat " . $request->latitude . "\n"
        . "Lng " . $request->longitude;

    $image->text($text, 20, $image->height() - 120, function ($font) {
        $font->size(22);
        $font->color('#ffffff');
        $font->align('left');
        $font->valign('top');
    });

    Storage::disk('public')->put($photoPath, (string) $image->encode());

} else {

    return back()->with('error', 'Format foto tidak valid');

}

    // ======================
    // VALIDASI LOKASI
    // ======================
    $userLat = $request->latitude;
    $userLng = $request->longitude;

    if (!$userLat || !$userLng) {
        return back()->with('error', 'Lokasi tidak terdeteksi!');
    }

    $officeLat = config('app.office_location.latitude');
    $officeLng = config('app.office_location.longitude');
    $radius = config('app.office_location.radius');

    $earthRadius = 6371000;

    $dLat = deg2rad($officeLat - $userLat);
    $dLng = deg2rad($officeLng - $userLng);

    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($userLat)) * cos(deg2rad($officeLat)) *
         sin($dLng/2) * sin($dLng/2);

    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earthRadius * $c;

    // ======================
// STATUS LOGIC (FINAL)
// ======================
$batasMasuk = now()->setTime(8, 15, 0);

// Tentukan status keterlambatan dulu
$status = $now->gt($batasMasuk) ? 'Terlambat' : 'Hadir';

// Tentukan approval berdasarkan radius
if ($distance > $radius) {
    $approvalStatus = 'Pending';
} else {
    $approvalStatus = 'Approved';
}


    // ======================
    // SIMPAN DATA
    // ======================
    Attendance::create([
        'user_id'   => auth()->id(),
        'date'      => $today,
        'check_in'  => $now->format('H:i:s'),
        'status'    => $status,
        'approval_status' => $approvalStatus,
        'photo' => $photoPath,
        'latitude'  => $userLat,
        'longitude' => $userLng,
    ]);

    return back()->with('success', 'Check-in berhasil.');
}

    public function checkOut(Request $request)
{
    $today = now()->format('Y-m-d');
    $now = now();

    // ======================
// BATAS AKHIR CHECK-OUT 17:00
// ======================
$batasAkhirCheckout = now()->setTime(21, 0, 0);

if ($now->gt($batasAkhirCheckout)) {
    return back()->with('error', 'Check-out maksimal sampai jam 21:00.');
}

$now = now();
$today = $now->toDateString();

$attendance = Attendance::where('user_id', auth()->id())
    ->whereDate('date', $today)
    ->first();

if (!$attendance) {
    return back()->with('error','Data absensi tidak ditemukan');
}

$checkoutTime = $now;

if ($now->format('H:i') >= '16:00' && $now->format('H:i') <= '21:00') {

    // tetap dihitung jam 16:00
    $checkoutTime = Carbon::parse($today.' 16:00:00');

}

$attendance->check_out = $checkoutTime;

$attendance->checkout_type = 'manual';

$attendance->checkout_approval_status = 'Approved';

/* =============================
   HITUNG JAM KERJA
============================= */

$checkIn = \Carbon\Carbon::parse($attendance->check_in);
$checkOut = \Carbon\Carbon::parse($checkoutTime);

$minutes = $checkIn->diffInMinutes($checkOut);

$attendance->work_hours = round($minutes / 60, 2);

/* ============================= */


$attendance->save();

    $attendance = Attendance::where('user_id', auth()->id())
        ->where('date', $today)
        ->first();

    if (!$attendance) {
        return back()->with('error', 'Anda belum check-in.');
    }

    if ($attendance->check_out) {
        return back()->with('error', 'Anda sudah check-out.');
    }

    // VALIDASI LOKASI
    $userLat = $request->latitude;
    $userLng = $request->longitude;

    if (!$userLat || !$userLng) {
        return back()->with('error', 'Lokasi tidak terdeteksi!');
    }

    $officeLat = config('app.office_location.latitude');
    $officeLng = config('app.office_location.longitude');
    $radius = config('app.office_location.radius');

    $attendanceModel = new Attendance();
$distance = $attendanceModel->calculateDistance($userLat, $userLng);

    // ======================
// LOGIC APPROVAL PULANG
// ======================

if ($distance > $radius) {
    $checkoutApproval = 'Pending';
} else {
    $checkoutApproval = 'Approved';
}

// ======================
    // JAM KERJA DIHITUNG MAKSIMAL 16:00
    // ======================
    $batasHitungPulang = now()->setTime(16, 0, 0);

    $jamPulangFinal = $now->gt($batasHitungPulang)
        ? $batasHitungPulang
        : $now;

// ======================
// CEK IZIN KULIAH
// ======================

$izinKuliah = CollegePermission::where('user_id', auth()->id())
    ->where('date', $today)
    ->where('status', 'approved')
    ->first();

// ======================
// CEK IZIN UMUM
// ======================

$izinUmum = \App\Models\GeneralLeave::where('user_id', auth()->id())
    ->where('date', $today)
    ->where('status','approved')
    ->first();

$leaveMinutes = 0;

if($izinUmum){
    $leaveMinutes = $this->calculateLeaveMinutes(
        $izinUmum->start_time,
        $izinUmum->end_time
    );
}

$status = $attendance->status;

if ($izinKuliah) {
    $status = 'Izin Kuliah';
}

// ======================
// HITUNG JAM KERJA
// ======================

$checkInTime = Carbon::parse($attendance->check_in);
$checkOutTime = Carbon::parse($jamPulangFinal);

// total menit kerja
$totalWorkMinutes = $checkInTime->diffInMinutes($checkOutTime);

// kurangi menit izin
$totalWorkMinutes = $totalWorkMinutes - $leaveMinutes;

// jangan sampai negatif
if ($totalWorkMinutes < 0) {
    $totalWorkMinutes = 0;
}

// konversi ke jam dan menit
$workHours = floor($totalWorkMinutes / 60);
$workMinutes = $totalWorkMinutes % 60;

    $attendance->update([
    'check_out' => $jamPulangFinal->format('H:i:s'),
    'checkout_approval_status' => $checkoutApproval,
    'status' => $status,
    'work_hours' => $workHours,
    'work_minutes' => $workMinutes
]);

    return back()->with('success', 'Check-out berhasil.');
}

/*
|--------------------------------------------------------------------------
| HITUNG MENIT IZIN DALAM JAM KERJA
|--------------------------------------------------------------------------
*/

private function calculateLeaveMinutes($leaveStart, $leaveEnd)
{
    $workStart = Carbon::createFromTime(8,0,0);
    $workEnd = Carbon::createFromTime(16,0,0);

    $leaveStart = Carbon::parse($leaveStart);
    $leaveEnd = Carbon::parse($leaveEnd);

    // cari overlap antara izin dan jam kerja
    $start = $leaveStart->greaterThan($workStart) ? $leaveStart : $workStart;
    $end = $leaveEnd->lessThan($workEnd) ? $leaveEnd : $workEnd;

    if ($start >= $end) {
        return 0;
    }

    return $start->diffInMinutes($end);
}

}
