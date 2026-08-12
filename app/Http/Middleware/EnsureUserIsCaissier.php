<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCaissier
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle($request, $next)
{
    if (auth()->check() && auth()->user()->role === 'caissier') {
        return $next($request);
    }

    if (auth()->check() && auth()->user()->role === 'admin') {
        return redirect('/admin'); // L'admin est renvoyé vers son panel
    }

    return redirect('/login');
}
}
