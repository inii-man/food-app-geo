<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next, $role)
    {
        // Cek apakah pengguna sudah login dan role-nya sesuai
        if (!Auth::check() || Auth::user()->role !== $role) {
            return redirect('/dashboard');  // Redirect ke halaman utama jika role tidak sesuai
        }

        return $next($request);
    }
}
