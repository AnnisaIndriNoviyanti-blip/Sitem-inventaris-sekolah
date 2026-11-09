<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\RotateAccountPasswords;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Rotasi password akun non-admin
// dilakukan setiap tanggal 1 pukul 00.00 WIB.
Schedule::command(RotateAccountPasswords::class)
    ->monthlyOn(1, '00:00')
    ->timezone('Asia/Jakarta');