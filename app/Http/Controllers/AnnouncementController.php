<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $events = Announcement::orderBy('start_date','desc')->get();
        return view('admin.announcements.index', compact('events'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        Announcement::create($request->all());

        return redirect()->route('announcements.index')
            ->with('success','Pengumuman berhasil dibuat');
    }

    public function destroy($id)
    {
        Announcement::findOrFail($id)->delete();

        return back()->with('success','Pengumuman dihapus');
    }
}
