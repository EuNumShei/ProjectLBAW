<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotBanned
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->profile->banned) {
            return $next($request);
        }

        Auth::logout();
        return redirect()->route('login')
        ->withErrors('You are banned!')->onlyInput('email');
    }
}
