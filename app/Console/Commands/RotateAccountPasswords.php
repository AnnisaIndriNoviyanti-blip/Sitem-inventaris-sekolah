<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RotateAccountPasswords extends Command
{
    protected $signature = 'accounts:rotate-passwords {--dry-run}';

    protected $description = 'Rotasi password bulanan akun non-admin';

    public function handle(): int
    {
        $jumlah = 0;
        $dryRun = $this->option('dry-run');

        // Mengambil bulan dan tahun saat ini
        $bulanIni = now()->format('Y-m');

        // Memilih akun kelas dan organisasi,
        // serta mengecualikan akun admin.
        User::whereIn('jenis_akun', ['kelas', 'organisasi'])
            ->where(function ($query) {
                $query->whereNull('role')
                      ->orWhere('role', '!=', 'admin');
            })
            ->chunkById(100, function ($users) use (
                &$jumlah,
                $dryRun,
                $bulanIni
            ) {
                foreach ($users as $user) {

                    // Cek apakah password sudah dirotasi
                    // pada bulan berjalan.
                    if (
                        $user->password_rotated_at &&
                        Carbon::parse($user->password_rotated_at)
                            ->format('Y-m') === $bulanIni
                    ) {
                        continue;
                    }

                    $jumlah++;

                    // Simulasi tanpa mengubah password.
                    if ($dryRun) {
                        $this->line(
                            "Akun {$user->username} akan dirotasi."
                        );

                        continue;
                    }

                    // Membuat password baru.
                    $passwordBaru = Str::random(12);

                    // Menyimpan password dengan hash.
                    $user->password = Hash::make($passwordBaru);

                    // Menyimpan password terenkripsi
                    // jika aplikasi membutuhkan tampilan password asli.
                    $user->password_encrypted =
                        Crypt::encryptString($passwordBaru);

                    // Mencatat waktu rotasi terakhir.
                    $user->password_rotated_at = now();

                    $user->save();
                }
            });

        // Menampilkan hasil proses.
        if ($dryRun) {
            $this->info(
                "SIMULASI SELESAI. {$jumlah} akun akan dirotasi. Password tidak diubah."
            );
        } else {
            $this->info(
                "ROTASI SELESAI. {$jumlah} akun berhasil diproses."
            );
        }

        return self::SUCCESS;
    }
}
