<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Periksa adakah peran user ada di dalam senarai $roles yang dibenarkan
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk ' . implode(' atau ', $roles));
        }

        return $next($request);
    }
}