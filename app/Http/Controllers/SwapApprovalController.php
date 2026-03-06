<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduleSwapRequest;
use Illuminate\Support\Facades\Auth;

class SwapApprovalController extends Controller
{

public function index()
{

$requests = ScheduleSwapRequest::where('target_user_id',Auth::id())
->where('target_status','Pending')
->latest()
->get();

return view('swap_approval.index',compact('requests'));

}

public function approve($id)
{

$req = ScheduleSwapRequest::findOrFail($id);

$req->target_status = 'Approved';

$req->save();

return back()->with('success','Anda menyetujui tukar jadwal');

}

public function reject($id)
{

$req = ScheduleSwapRequest::findOrFail($id);

$req->target_status = 'Rejected';
$req->status = 'Rejected';

$req->save();

return back()->with('success','Anda menolak tukar jadwal');

}

}
