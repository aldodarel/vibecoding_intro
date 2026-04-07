<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect('/login');
        }

        if (! $request->user()->isOwner()) {
            return redirect('/renter/dashboard')->with('error', 'Akses tidak diizinkan');
        }

        return $next($request);
    }
}
