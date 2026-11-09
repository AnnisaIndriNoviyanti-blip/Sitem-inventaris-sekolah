<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ResetAdminSecurity extends Command
{
    protected $signature = 'admin:reset-security {username}';

    protected $description =
        'Reset password dan pertanyaan keamanan Admin TU saat pergantian petugas';

    public function handle(): int
    {
        $username = $this->argument('username');

        $admin = User::where('username', $username)
            ->where('role', 'admin_tu')
            ->first();

        if (!$admin) {
            $this->error('Akun Admin TU tidak ditemukan.');
            return self::FAILURE;
        }

        $this->warn(
            'Tindakan ini akan menghapus pengamanan Admin TU lama.'
        );

        if (!$this->confirm(
            'Apakah Anda yakin ingin melanjutkan?'
        )) {
            $this->info('Proses dibatalkan.');
            return self::SUCCESS;
        }

        // Minta password sementara secara tersembunyi.
        $password = $this->secret(
            'Masukkan password sementara untuk Admin TU baru'
        );

        if (!$password || strlen($password) < 8) {
            $this->error(
                'Password sementara minimal 8 karakter.'
            );

            return self::FAILURE;
        }

        $konfirmasi = $this->secret(
            'Ulangi password sementara'
        );

        if ($password !== $konfirmasi) {
            $this->error('Konfirmasi password tidak cocok.');
            return self::FAILURE;
        }

        DB::transaction(function () use ($admin, $password) {

            $admin->update([
                'password' => $password,

                // Hapus data keamanan petugas lama.
                'security_question' => null,
                'security_answer' => null,
                'security_answer_updated_at' => null,

                // Ganti token pengingat login.
                'remember_token' => Str::random(60),
            ]);

            // Jika menggunakan database session,
            // hapus sesi login Admin TU yang lama.
            if (
                Schema::hasTable('sessions') &&
                Schema::hasColumn('sessions', 'user_id')
            ) {
                DB::table('sessions')
                    ->where('user_id', $admin->id)
                    ->delete();
            }
        });

        $this->info(
            'Password dan pengamanan Admin TU berhasil direset.'
        );

        $this->info(
            'Admin TU baru harus mengatur pertanyaan keamanan kembali.'
        );

        return self::SUCCESS;
    }
}
