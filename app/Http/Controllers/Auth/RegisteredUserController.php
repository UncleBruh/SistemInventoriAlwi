<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:'.User::class.',username'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:Admin,Pemilik'],
        ]);

        // Simpan ke database
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password), // Password WAJIB di-Hash
            'role' => $request->role,
        ]);

        event(new Registered($user));

        // Langsung login otomatis setelah daftar
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}