<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SessionAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->session()->get('user_id')) {
            return redirect()->route('login.form')->with('error', 'Silakan login dahulu.');
        }

        return $next($request);
    }
}
