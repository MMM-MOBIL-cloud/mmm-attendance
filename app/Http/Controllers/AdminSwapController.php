<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScheduleSwapRequest;

class AdminSwapController extends Controller
{

    public function index()
    {
        $requests = ScheduleSwapRequest::with(['requester','targetUser'])
                    ->orderBy('created_at','desc')
                    ->get();

        return view('admin.swap_requests', compact('requests'));
    }

    public function approve($id)
    {
        $req = ScheduleSwapRequest::findOrFail($id);

        $req->status = 'Approved';
        $req->save();

        $fromDay = \Carbon\Carbon::parse($req->from_date)->format('l');
        $toDay = \Carbon\Carbon::parse($req->to_date)->format('l');

        // ======================
        // SELF SWAP
        // ======================
        if(!$req->target_user_id){

            $userId = $req->requester_id;

            \DB::table('user_work_days')
                ->where('user_id', $userId)
                ->where('day', $fromDay)
                ->delete();

            \DB::table('user_work_days')->insert([
                'user_id' => $userId,
                'day' => $toDay,
                'created_at' => now(),
                'updated_at' => now()
            ]);

        }

        // ======================
        // SWAP DENGAN USER LAIN
        // ======================
        else{

            $userA = $req->requester_id;
            $userB = $req->target_user_id;

            \DB::table('user_work_days')
                ->where('user_id',$userA)
                ->where('day',$fromDay)
                ->delete();

            \DB::table('user_work_days')
                ->where('user_id',$userB)
                ->where('day',$toDay)
                ->delete();

            \DB::table('user_work_days')->insert([
                'user_id'=>$userA,
                'day'=>$toDay,
                'created_at'=>now(),
                'updated_at'=>now()
            ]);

            \DB::table('user_work_days')->insert([
                'user_id'=>$userB,
                'day'=>$fromDay,
                'created_at'=>now(),
                'updated_at'=>now()
            ]);
        }

        return back()->with('success','Request tukar jadwal berhasil disetujui');
    }

    public function reject($id)
    {
        $req = ScheduleSwapRequest::findOrFail($id);

        $req->update([
            'status' => 'Rejected'
        ]);

        return back()->with('success','Request tukar jadwal ditolak');
    }

}
