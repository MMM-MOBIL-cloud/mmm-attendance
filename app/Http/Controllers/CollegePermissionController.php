<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CollegePermission;
use Illuminate\Support\Facades\Auth;

class CollegePermissionController extends Controller
{

    public function create()
    {
        return view('college_permission.create');
    }

    public function store(Request $request)
    {

        CollegePermission::create([
            'user_id' => Auth::id(),

            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,

            'replace_date' => $request->replace_date,
            'replace_start' => $request->replace_start,
            'replace_end' => $request->replace_end,
            'replace_reason' => $request->replace_reason,

            'status' => 'pending'
        ]);

        return redirect()->back()->with('success','Izin kuliah berhasil diajukan');
    }

    public function index(Request $request)
{
    $query = \App\Models\CollegePermission::with('user');

    if ($request->month) {
        $query->whereMonth('date', $request->month);
    }

    if ($request->year) {
        $query->whereYear('date', $request->year);
    }

    $permissions = $query->latest()->get();

    return view('admin.college_permissions.index', compact('permissions'));
}

public function approve($id)
{
    $permission = \App\Models\CollegePermission::findOrFail($id);

    $permission->status = 'approved';
    $permission->save();

    return back()->with('success','Izin kuliah berhasil diapprove');
}

public function reject($id)
{
    $permission = \App\Models\CollegePermission::findOrFail($id);

    $permission->status = 'rejected';
    $permission->save();

    return back()->with('error','Izin kuliah ditolak');
}
}
