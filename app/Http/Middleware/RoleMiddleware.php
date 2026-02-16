<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah Role user sesuai dengan yang diminta route
        // $role adalah parameter dari route (misal: 'admin')
        if (Auth::user()->role !== $role) {
            // Jika role beda, tendang ke dashboard masing-masing atau error 403
            return abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}