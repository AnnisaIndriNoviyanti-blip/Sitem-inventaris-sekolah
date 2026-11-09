<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    
    public function showLogin()
    {
        return view('masuk');
    }





public function login(Request $request)
{
    $credentials = $request->validate([
        'username' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    $user = User::with('organisasi')
        ->where('username', $credentials['username'])
        ->first();

    if (
        !$user ||
        $user->status !== 'aktif' ||
        !Hash::check(
            $credentials['password'],
            $user->password
        )
    ) {
        return back()
            ->withErrors([
                'username' =>
                    'Username atau password salah, atau akun tidak aktif.',
            ])
            ->onlyInput('username');
    }

    // Login berhasil.
    Auth::login($user);
    $request->session()->regenerate();

    // Admin tetap masuk dashboard admin.
    if ($user->role === 'admin_tu') {
        return redirect()->route('admin.dashboard');
    }

    // Akun guru/siswa diperiksa berdasarkan jenis akunnya.
    if (in_array($user->role, ['guru', 'siswa'], true)) {

        if ($user->jenis_akun === 'organisasi') {

            // Akun organisasi wajib terhubung ke organisasi aktif.
            if (
                !$user->organisasi_id ||
                !$user->organisasi ||
                !$user->organisasi->aktif
            ) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors([
                        'username' =>
                            'Akun ini belum terhubung dengan '
                            . 'organisasi aktif yang valid.',
                    ]);
            }

            return redirect()->route('organisasi.dashboard');
        }

        // Akun kelas harus memiliki jenis akun kelas.
        if ($user->jenis_akun === 'kelas') {
            if (!$user->kelas_id) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors([
                        'username' =>
                            'Akun ini belum terhubung dengan kelas.',
                    ]);
            }

            return redirect()->route('kelas.dashboard');
        }
    }

    // Jenis akun atau peran tidak valid.
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')
        ->withErrors([
            'username' => 'Peran atau jenis akun tidak valid.',
        ]);
}


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
