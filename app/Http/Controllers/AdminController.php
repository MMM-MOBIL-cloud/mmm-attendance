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

    public function attendanceToday(Request $request)
{

    $query = Attendance::with('user');

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
    $user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'role' => $request->role,
    'position' => $request->position,
    'shift_start' => $request->shift_start,
    'shift_end' => $request->shift_end,
]);

    if($request->work_days){

        foreach($request->work_days as $day){

            \DB::table('user_work_days')->insert([
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
    $user = User::findOrFail($id);

    $user->update([
        'position' => $request->position,

        'can_swap_schedule' => $request->has('can_swap_schedule'),
        'can_approve_swap' => $request->has('can_approve_swap'),
        'can_student_leave' => $request->has('can_student_leave'),
        'can_general_leave' => $request->has('can_general_leave'),
    ]);

    return redirect()->route('admin.users')
        ->with('success','Jabatan berhasil diupdate');
}

public function deleteUser($id)
{
    $user = User::findOrFail($id);

    $user->delete();

    return redirect()->route('admin.users')
        ->with('success','User berhasil dihapus');
}

}
