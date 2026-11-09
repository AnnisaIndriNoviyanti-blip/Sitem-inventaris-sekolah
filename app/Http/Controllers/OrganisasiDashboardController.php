<?php

namespace App\Http\Controllers;

class OrganisasiDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Hanya akun guru/siswa bertipe organisasi
        // yang boleh masuk ke dashboard ini.
        abort_unless(
            $user &&
            in_array($user->role, ['guru', 'siswa'], true) &&
            $user->jenis_akun === 'organisasi' &&
            $user->organisasi_id !== null &&
            $user->organisasi &&
            $user->organisasi->aktif,
            403
        );

        return view('organisasi.dashboard');
    }
}
