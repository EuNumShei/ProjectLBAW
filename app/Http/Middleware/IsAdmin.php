<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->profile->admin) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Unauthorized access');
    }
}
