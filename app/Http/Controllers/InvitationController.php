<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email'
        ]);

        $token = Str::random(32);

        $user = User::create([
            'name' => 'Karyawan',
            'email' => $request->email,
            'password' => bcrypt(Str::random(10)),
            'invitation_token' => $token,
            'is_active' => false,
        ]);

        $link = url('/invite-register/'.$token);

        Mail::raw("Anda diundang untuk bergabung ke sistem Absensi MMM MOBIL.\n\nKlik link berikut untuk membuat akun:\n".$link, function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Undangan Bergabung - Absensi MMM MOBIL');
        });

        return back()->with('success','Undangan berhasil dikirim.');
    }


    public function showRegister($token)
    {
        $user = User::where('invitation_token', $token)->firstOrFail();

        return view('auth.invite-register', compact('token'));
    }


    public function processRegister(Request $request, $token)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::where('invitation_token', $token)->firstOrFail();

        $user->update([
            'name' => $request->name,
            'password' => bcrypt($request->password),
            'invitation_token' => null,
            'is_active' => true
        ]);

        return redirect('/login')->with('success','Akun berhasil dibuat, silakan login.');
    }
}
