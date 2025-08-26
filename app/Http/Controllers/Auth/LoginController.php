<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function showLoginForm()
    {
        return view('auth.login'); // Pastikan view login Anda ada di resources/views/auth/login.blade.php
    }

    /**
     * Menangani permintaan login.
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Coba untuk melakukan autentikasi
        if (Auth::attempt($credentials, $request->boolean('remember-me'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Arahkan pengguna berdasarkan peran (role)
            switch ($user->role) {
                case 'admin':
                    return redirect()->intended('/admin/dashboard'); // Ganti dengan route dashboard admin Anda
                case 'guru':
                    return redirect()->intended('/guru/dashboard'); // Ganti dengan route dashboard guru Anda
                case 'siswa':
                    return redirect()->intended('/siswa/dashboard'); // Ganti dengan route dashboard siswa Anda
                default:
                    return redirect('/');
            }
        }

        // Jika autentikasi gagal
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Menangani permintaan logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
