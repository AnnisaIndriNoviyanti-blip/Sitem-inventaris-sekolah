<?php

namespace App\Http\Controllers;

use App\Models\BarangTu;
use App\Models\Peminjaman;
use App\Models\LaporanKerusakan;

class AdminDashboardController extends Controller
{
    public function index()
    {
        abort_unless(
            auth()->user()->role === 'admin_tu',
            403
        );

        // Statistik barang
        $totalBarang = BarangTu::count();

        $totalUnitBarang = BarangTu::count();
        // Menghitung barang yang tersedia dan kondisinya baik
        $barangTersedia = BarangTu::where('status', 'tersedia')
        ->where('kondisi', 'baik')
        ->count();

        $sedangDipinjam = BarangTu::where(
            'status',
            'dipinjam'
        )->count();

        // Jumlah permintaan yang menunggu verifikasi
        $menungguVerifikasi = Peminjaman::where(
            'status',
            'menunggu'
        )->count();

        // Ambil daftar permintaan menunggu untuk ditampilkan
        $permintaanMenunggu = Peminjaman::with([
            'user',
            'detail.barang',
        ])
            ->where('status', 'menunggu')
            ->latest()
            ->take(5)
            ->get();

        // Jumlah laporan kerusakan yang belum selesai
        $jumlahKerusakan = LaporanKerusakan::whereIn(
            'status',
            ['menunggu', 'diproses']
        )->count();

        // Barang yang paling sering dipinjam
        $barangTerpopuler = Peminjaman::join(
            'peminjaman_detail',
            'peminjaman.id',
            '=',
            'peminjaman_detail.peminjaman_id'
        )
            ->join(
                'barang_tu',
                'barang_tu.id',
                '=',
                'peminjaman_detail.barang_tu_id'
            )
            ->selectRaw(
                'barang_tu.nama_barang as nama,
                COUNT(peminjaman_detail.id) as jumlah'
            )
            ->groupBy(
                'barang_tu.id',
                'barang_tu.nama_barang'
            )
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalBarang',
            'totalUnitBarang',
            'barangTersedia',
            'sedangDipinjam',
            'menungguVerifikasi',
            'permintaanMenunggu',
            'jumlahKerusakan',
            'barangTerpopuler'
        ));
    }
}
