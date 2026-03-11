<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\CollegePermission;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index()
    {
        if(Auth::user()->role == 'admin'){
            return redirect()->route('admin.dashboard');
        }

        $today = now()->format('Y-m-d');

        $attendanceToday = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $attendanceHistory = Attendance::where('user_id', Auth::id())
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->orderBy('date','desc')
            ->get();

        $totalHadirBulanIni = Attendance::where('user_id', Auth::id())
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->whereNotNull('check_in')
            ->count();

        $totalTerlambatBulanIni = Attendance::where('user_id', Auth::id())
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('status','Terlambat')
            ->count();

        // hitung total menit terlambat
        $batasMasuk = Carbon::createFromTime(8,15,0);
        $totalMenitTerlambat = 0;

        $absensiTerlambat = Attendance::where('user_id', Auth::id())
            ->whereMonth('date',$currentMonth)
            ->whereYear('date',$currentYear)
            ->where('status','Terlambat')
            ->get();

        foreach($absensiTerlambat as $absen){
            $jamMasuk = Carbon::parse($absen->check_in);
            $selisih = $batasMasuk->diffInMinutes($jamMasuk,false);

            if($selisih > 0){
                $totalMenitTerlambat += $selisih;
            }
        }

        $totalJamTerlambat = floor($totalMenitTerlambat / 60);
        $sisaMenitTerlambat = $totalMenitTerlambat % 60;

        $totalBelumPulang = Attendance::where('user_id', Auth::id())
            ->whereMonth('date',$currentMonth)
            ->whereYear('date',$currentYear)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->count();

        $izinKuliahHistory = CollegePermission::where('user_id', Auth::id())
            ->orderBy('date','desc')
            ->get();

        return view('dashboard', compact(
            'attendanceToday',
            'attendanceHistory',
            'totalHadirBulanIni',
            'totalTerlambatBulanIni',
            'totalJamTerlambat',
            'sisaMenitTerlambat',
            'totalBelumPulang',
            'izinKuliahHistory'
        ));
    }
}
