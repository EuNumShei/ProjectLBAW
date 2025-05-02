<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PasswordController extends Controller
{
    public function showResetForm(Request $request)
    {
        return view('auth.reset')->with([
            'token' => $request->token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'token' => 'required',
        ]);


        //Log::info('Received token: ' . $request->token);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if ($resetRecord) {
            Log::info('Expected token: ' . $resetRecord->token);
        } else {
            Log::info('No reset record found for email: ' . $request->email);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->setRememberToken(Str::random(60));
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('success', 'Password has been reset!')
                    : back()->withErrors(['email' => [__($status)]]);
    }
}