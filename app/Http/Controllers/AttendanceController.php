<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\AttendanceRejectedNotification;

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
        $imageName = 'absen_' . time() . '.png';

        Storage::disk('public')->put(
            $imageName,
            base64_decode($imageBase64)
        );
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
    // STATUS LOGIC
    // ======================
    $batasMasuk = now()->setTime(8, 0, 0);

    if ($distance > $radius) {
    $status = 'Di Luar Radius';
    $approvalStatus = 'Pending';
} else {
    $approvalStatus = 'Approved';

    if ($now->gt($batasMasuk)) {
        $status = 'Terlambat';
    } else {
        $status = 'Hadir';
    }
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
        'photo'     => $imageName,
        'latitude'  => $userLat,
        'longitude' => $userLng,
    ]);

    return back()->with('success', 'Check-in berhasil.');
}

    public function checkOut(Request $request)
{
    $today = now()->format('Y-m-d');
    $now = now();

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

    $attendance->update([
        'check_out' => $now->format('H:i:s'),
        'checkout_approval_status' => $checkoutApproval
    ]);

    return back()->with('success', 'Check-out berhasil.');
}
}
