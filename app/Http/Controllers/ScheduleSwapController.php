<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ScheduleSwapRequest;
use Illuminate\Support\Facades\Auth;

class ScheduleSwapController extends Controller
{

    public function create()
    {
        $users = User::where('role','user')->get();

        return view('schedule_swap.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
    'from_date' => 'required|date',
    'to_date' => 'required|date',
]);

        ScheduleSwapRequest::create([
    'requester_id' => Auth::id(),
    'target_user_id' => $request->target_user_id,

    'from_date' => $request->from_date,
    'to_date' => $request->to_date,

    'type' => $request->type,

    'status' => 'Pending',
    'target_status' => 'Pending'
]);

        return back()->with('success','Request tukar jadwal berhasil dikirim.');
    }

    public function index()
{

    $requests = ScheduleSwapRequest::where('requester_id', Auth::id())
        ->latest()
        ->get();

    return view('schedule_swap.index', compact('requests'));

}

}
