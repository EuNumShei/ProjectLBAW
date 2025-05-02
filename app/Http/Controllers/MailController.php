<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Password;
use App\Models\User;


class MailController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);


        if (!User::whereRaw('LOWER(email) = ?', [strtolower($request->email)])->exists()) {
            return redirect()->back()->withErrors(['email' => 'Email not found']);
        }

        $user = User::whereRaw('LOWER(email) = ?', [strtolower($request->email)])->first();

        $token = Password::createToken($user);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $mailData = [
            'name' => User::whereRaw('LOWER(email) = ?', [strtolower($request->email)])->first()->name,
            'reset_link' => url('/password/reset?token=' . $token . '&email=' . urlencode($request->email)),
        ];

        Mail::to($request->email)->send(new MailModel($mailData));

        return redirect()->route('login')->with('success', 'Password recovery email sent!');
    }
}