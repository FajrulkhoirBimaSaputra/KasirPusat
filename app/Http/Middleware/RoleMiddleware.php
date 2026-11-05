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
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek apakah user belum login, atau role-nya TIDAK ADA di dalam daftar role yang diizinkan
        if (! Auth::check() || ! in_array(Auth::user()->role, $roles)) {
            abort(403, 'Akses Ditolak! Anda tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}