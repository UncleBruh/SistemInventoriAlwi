<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CekRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek apakah role user saat ini sama dengan role yang disyaratkan di route
        if (Auth::user()->role !== $role) {
            // Jika admin mencoba masuk ke halaman pemilik, kembalikan 403 (Unauthorized)
            abort(403, 'Akses ditolak. Halaman ini hanya untuk ' . $role);
        }

        return $next($request);
    }
}