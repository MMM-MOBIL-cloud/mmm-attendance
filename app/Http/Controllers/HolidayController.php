<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = DB::table('holidays')
            ->orderBy('date')
            ->get();

        return view('admin.holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        DB::table('holidays')->insert([
            'title' => $request->title,
            'date' => $request->date,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success','Libur berhasil ditambahkan');
    }

    public function storeAjax(Request $r)
{
    DB::table('holidays')->insert([
        'title' => $r->title,
        'date' => $r->date,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return response()->json(['success'=>true]);
}

    public function delete($id)
    {
        DB::table('holidays')->where('id',$id)->delete();

        return redirect()->back()->with('success','Libur berhasil dihapus');
    }
}
