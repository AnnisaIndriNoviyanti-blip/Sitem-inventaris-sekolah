<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AdminSecurityController extends Controller
{
    // Daftar pertanyaan yang disediakan sistem.
    public const QUESTIONS = [
        'Apa nama kota tempat Anda dilahirkan?',
        'Siapa nama saudara pertama Anda?',
        'Apa nama sekolah dasar Anda?',
        'Apa nama panggilan masa kecil Anda?',
        'Apa nama hewan peliharaan pertama Anda?',
        'Apa nama jalan tempat Anda dibesarkan?',
        'Apa makanan favorit Anda saat kecil?',
        'Siapa nama guru favorit Anda?',
    ];

    // Memastikan hanya Admin TU yang bisa mengakses.
    private function pastikanAdmin(): void
    {
        abort_unless(
            auth()->check() &&
            auth()->user()->role === 'admin_tu',
            403
        );
    }

    // Menampilkan halaman pengaturan keamanan.
    public function edit()
    {
        $this->pastikanAdmin();

        return view('admin.pengamanan.index', [
            'questions' => self::QUESTIONS,
            'admin' => auth()->user(),
        ]);
    }

    // Menyimpan pertanyaan dan jawaban keamanan.
    public function update(Request $request)
    {
        $this->pastikanAdmin();

        $data = $request->validate([
            'security_question' => [
                'required',
                'string',
                'in:' . implode(',', self::QUESTIONS),
            ],
            'security_answer' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'confirmed',
            ],
            'current_password' => [
                'required',
                'string',
            ],
        ]);

        $admin = auth()->user();

        // Memeriksa password saat ini.
        if (!Hash::check(
            $data['current_password'],
            $admin->password
        )) {
            return back()
                ->withErrors([
                    'current_password' =>
                        'Password saat ini tidak sesuai.',
                ])
                ->withInput();
        }

        // Menyamakan huruf besar/kecil dan menghapus
        // spasi di awal serta akhir jawaban.
        $jawaban = Str::lower(
            trim($data['security_answer'])
        );

        $admin->update([
            'security_question' =>
                $data['security_question'],

            'security_answer' =>
                Hash::make($jawaban),

            'security_answer_updated_at' => now(),
        ]);

        return back()->with(
            'success',
            'Pengaturan keamanan berhasil disimpan.'
        );
    }
    public function resetSecurity(Request $request)
{
    abort_unless(auth()->user()?->role === 'admin_tu', 403);

    $request->validate([
        'current_password' => ['required', 'current_password'],
    ], [
        'current_password.current_password' => 'Password saat ini tidak sesuai.',
    ]);

    $admin = auth()->user();

    // Hapus pengaturan pertanyaan dan jawaban keamanan.
    $admin->update([
        'security_question' => null,
        'security_answer' => null,
        'security_answer_updated_at' => null,
    ]);

    return redirect()
        ->route('admin.pengamanan.index')
        ->with('success', 'Pengamanan akun berhasil direset. Silakan buat pertanyaan keamanan baru.');
}

    // Menampilkan halaman lupa password.
    public function forgotForm()
    {
        return view('auth.forgot-password');
    }

    // Mencari akun Admin TU berdasarkan username.
    public function showQuestion(Request $request)
    {
        $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $username = trim($request->username);

        $key = 'recovery:' . $request->ip()
            . ':' . Str::lower($username);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors([
                'username' =>
                    'Terlalu banyak percobaan. Coba lagi dalam 15 menit.',
            ]);
        }

        $admin = User::where('username', $username)
            ->where('role', 'admin_tu')
            ->first();

        if (
            !$admin ||
            !$admin->security_question ||
            !$admin->security_answer
        ) {
            RateLimiter::hit($key, 900);

            return back()->withErrors([
                'username' =>
                    'Data pemulihan tidak ditemukan atau belum diatur.',
            ]);
        }

        // Simpan username sementara di session.
        session([
            'recovery.username' => $username,
        ]);

        return view('auth.security-question', [
            'question' => $admin->security_question,
        ]);
    }

    // Memeriksa jawaban keamanan.
    public function verifyAnswer(Request $request)
    {
        $request->validate([
            'security_answer' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $username = session('recovery.username');

        if (!$username) {
            return redirect()->route('password.request');
        }

        $key = 'recovery:' . $request->ip()
            . ':' . Str::lower($username);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            session()->forget('recovery.username');

            return redirect()
                ->route('password.request')
                ->withErrors([
                    'username' =>
                        'Terlalu banyak percobaan. Silakan coba lagi nanti.',
                ]);
        }

        $admin = User::where('username', $username)
            ->where('role', 'admin_tu')
            ->first();

        $jawaban = Str::lower(
            trim($request->security_answer)
        );

        if (
            !$admin ||
            !$admin->security_answer ||
            !Hash::check(
                $jawaban,
                $admin->security_answer
            )
        ) {
            RateLimiter::hit($key, 900);

            return back()->withErrors([
                'security_answer' =>
                    'Jawaban keamanan tidak sesuai.',
            ]);
        }

        // Jawaban benar.
        RateLimiter::clear($key);

        // Membuat izin pemulihan selama 10 menit.
        session([
            'recovery.verified_user_id' => $admin->id,
            'recovery.expires_at' =>
                now()->addMinutes(10)->timestamp,
        ]);

        session()->forget('recovery.username');

        return redirect()->route('password.reset.form');
    }

    // Memeriksa apakah sesi pemulihan masih berlaku.
    private function recoveryValid(): bool
    {
        return session()->has('recovery.verified_user_id')
            && session()->has('recovery.expires_at')
            && now()->timestamp <= session('recovery.expires_at');
    }

    // Menampilkan form password baru.
    public function resetForm()
    {
        if (!$this->recoveryValid()) {
            return redirect()
                ->route('password.request')
                ->withErrors([
                    'username' =>
                        'Sesi pemulihan sudah berakhir.',
                ]);
        }

        return view('auth.reset-password');
    }

    // Menyimpan password baru.
    public function resetPassword(Request $request)
    {
        if (!$this->recoveryValid()) {
            session()->forget([
                'recovery.username',
                'recovery.verified_user_id',
                'recovery.expires_at',
            ]);

            return redirect()
                ->route('password.request')
                ->withErrors([
                    'username' =>
                        'Sesi pemulihan sudah berakhir.',
                ]);
        }

        $data = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $admin = User::where(
            'id',
            session('recovery.verified_user_id')
        )
            ->where('role', 'admin_tu')
            ->firstOrFail();

        // Cast hashed pada model User akan
        // otomatis meng-hash password baru.
        $admin->update([
            'password' => $data['password'],
        ]);

        session()->forget([
            'recovery.username',
            'recovery.verified_user_id',
            'recovery.expires_at',
        ]);

        session()->regenerate();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Password berhasil diperbarui. Silakan login.'
            );
    }
}
