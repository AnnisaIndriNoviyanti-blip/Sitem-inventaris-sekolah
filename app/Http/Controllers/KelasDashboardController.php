<?php

namespace App\Http\Controllers;

use App\Models\BarangTu;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use Illuminate\Http\Request;

class KelasDashboardController extends Controller
{
    public function index()
    {
        abort_unless(
            auth()->user()->jenis_akun === 'kelas',
            403
        );
        abort_unless(
            in_array(auth()->user()->role, ['guru', 'siswa']),
            403
        );

        $userId = auth()->id();

        // Statistik peminjaman milik pengguna yang login.
        $peminjamanUser = Peminjaman::where('user_id', $userId);

        $totalPeminjaman = (clone $peminjamanUser)->count();

        $sedangDipinjam = (clone $peminjamanUser)
            ->where('status', 'dipinjam')
            ->count();

        $peminjamanDiproses = (clone $peminjamanUser)
            ->where('status', 'menunggu')
            ->count();

        // Jumlah laporan kerusakan dari peminjaman milik pengguna.
        $totalLaporanKerusakan = (clone $peminjamanUser)
            ->withCount('laporanKerusakan')
            ->get()
            ->sum('laporan_kerusakan_count');

        // Barang yang tersedia untuk dipinjam.
        $barangTersedia = BarangTu::where('status', 'tersedia')
            ->where('kondisi', 'baik')
            ->count();

        // Lima barang yang paling sering diajukan dalam peminjaman.
        $barangPopuler = PeminjamanDetail::query()
            ->selectRaw(
                'barang_tu_id, SUM(jumlah) as total_dipinjam'
            )
            ->groupBy('barang_tu_id')
            ->orderByDesc('total_dipinjam')
            ->with('barang')
            ->take(5)
            ->get();

        // Riwayat peminjaman terbaru milik pengguna.
        $peminjamanTerbaru = (clone $peminjamanUser)
            ->with(['detail.barang'])
            ->latest()
            ->take(5)
            ->get();

        return view('kelas.dashboard', compact(
            'totalPeminjaman',
            'sedangDipinjam',
            'peminjamanDiproses',
            'totalLaporanKerusakan',
            'barangTersedia',
            'barangPopuler',
            'peminjamanTerbaru'
        ));
    }
}
