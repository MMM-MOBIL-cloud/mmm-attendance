<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralLeave;
use Illuminate\Support\Facades\Auth;

class GeneralLeaveController extends Controller
{

    public function index()
    {
        $leaves = GeneralLeave::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('general_leave.index', compact('leaves'));
    }

    public function adminIndex()
{
    $leaves = GeneralLeave::with('user')
        ->latest()
        ->get();

    return view('admin.izin.index', compact('leaves'));
}

    public function approve($id)
{
    $leave = GeneralLeave::findOrFail($id);

    $leave->update([
        'status' => 'approved'
    ]);

    return back()->with('success','Izin berhasil disetujui');
}

public function reject($id)
{
    $leave = GeneralLeave::findOrFail($id);

    $leave->update([
        'status' => 'rejected'
    ]);

    return back()->with('success','Izin ditolak');
}

    public function create()
    {
        return view('general_leave.create');
    }

    public function store(Request $request)
    {
    $request->validate([
        'date' => 'required|date',
        'reason' => 'required'
    ]);

    $proofFile = null;

    if ($request->hasFile('proof')) {

        $file = $request->file('proof');

        $filename = time().'_'.$file->getClientOriginalName();

        $file->storeAs('leave_proofs', $filename, 'public');

        $proofFile = $filename;
    }

    GeneralLeave::create([
        'user_id' => Auth::id(),
        'date' => $request->date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'reason' => $request->reason,
        'proof' => $proofFile,
        'status' => 'pending'
    ]);

    return redirect()->route('izin.index')
        ->with('success','Pengajuan izin berhasil dikirim');
}

}
