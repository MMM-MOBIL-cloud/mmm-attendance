<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminController extends Controller
{

    public function users()
    {
        $users = User::where('role','user')->get();

        return view('admin.users', compact('users'));
    }

    public function attendanceToday()
    {
        $attendances = Attendance::whereDate('date', today())
                        ->with('user')
                        ->get();

        return view('admin.attendance_today', compact('attendances'));
    }

    public function storeUser(Request $request)
{
    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'shift_start' => $request->shift_start,
        'shift_end' => $request->shift_end,
    ]);

    return redirect()->back()->with('success', 'User berhasil dibuat');
}

}
