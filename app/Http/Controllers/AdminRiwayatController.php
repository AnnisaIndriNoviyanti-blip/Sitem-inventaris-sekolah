<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\BarangTu;
use App\Models\LaporanKerusakan;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class AdminRiwayatController extends Controller
{
    public function index(Request $request)
    {
        // Pastikan hanya admin TU yang bisa mengakses
        abort_unless(
            auth()->check() &&
            auth()->user()->role === 'admin_tu',
            403
        );

        // Validasi filter
        $request->validate([
            'cari' => ['nullable', 'string', 'max:255'],
            'status' => [
                'nullable',
                'in:semua,menunggu,disetujui,ditolak,dipinjam,dikembalikan'
            ],
            'dari' => ['nullable', 'date'],
            'sampai' => [
                'nullable',
                'date',
                'after_or_equal:dari'
            ],
        ]);

        // Ringkasan seluruh transaksi
        $totalTransaksi = Peminjaman::count();

        $totalDikembalikan = Peminjaman::where(
            'status',
            'dikembalikan'
        )->count();

        $totalDitolak = Peminjaman::where(
            'status',
            'ditolak'
        )->count();

        $totalLaporan = LaporanKerusakan::count();

        // Data riwayat peminjaman
        $query = Peminjaman::with([
            'user',
            'detail.barang',
            'laporanKerusakan',
        ])
        ->latest('tanggal_pinjam')
        ->latest('id');

        // Filter status
        if (
            $request->filled('status') &&
            $request->status !== 'semua'
        ) {
            $query->where('status', $request->status);
        }

        // Filter tanggal awal
        if ($request->filled('dari')) {
            $query->whereDate(
                'tanggal_pinjam',
                '>=',
                $request->dari
            );
        }

        // Filter tanggal akhir
        if ($request->filled('sampai')) {
            $query->whereDate(
                'tanggal_pinjam',
                '<=',
                $request->sampai
            );
        }

        // Pencarian berdasarkan peminjam,
        // keperluan, kode, catatan, dan barang
        if ($request->filled('cari')) {
            $cari = trim($request->cari);

            $query->where(function (Builder $q) use ($cari) {
                $q->where(
                    'nama_peminjam',
                    'like',
                    "%{$cari}%"
                )
                ->orWhere(
                    'keperluan',
                    'like',
                    "%{$cari}%"
                )
                ->orWhere(
                    'catatan',
                    'like',
                    "%{$cari}%"
                )
                ->orWhere(
                    'id',
                    'like',
                    "%{$cari}%"
                )
                ->orWhereHas('user', function ($user) use ($cari) {
                    $user->where(
                        'nama',
                        'like',
                        "%{$cari}%"
                    );
                })
                ->orWhereHas(
                    'detail.barang',
                    function ($barang) use ($cari) {
                        $barang->where(
                            'nama_barang',
                            'like',
                            "%{$cari}%"
                        )
                        ->orWhere(
                            'kode_barang',
                            'like',
                            "%{$cari}%"
                        );
                    }
                );
            });
        }

        // Pagination riwayat
        $peminjaman = $query
            ->paginate(10)
            ->withQueryString();

        // Rekap penggunaan barang
        $rekapBarang = BarangTu::query()
            ->leftJoin(
                'peminjaman_detail as pd',
                'pd.barang_tu_id',
                '=',
                'barang_tu.id'
            )
            ->leftJoin(
                'peminjaman as p',
                'p.id',
                '=',
                'pd.peminjaman_id'
            )
            ->select([
                'barang_tu.id',
                'barang_tu.kode_barang',
                'barang_tu.nama_barang',
                'barang_tu.jenis_barang',
            ])
            ->selectRaw(
                'COUNT(DISTINCT p.id) as total_transaksi'
            )
            ->selectRaw(
                'COALESCE(SUM(pd.jumlah), 0) as total_unit'
            )
            ->selectRaw("
                COALESCE(
                    SUM(
                        CASE
                            WHEN p.status = 'dipinjam'
                            THEN pd.jumlah
                            ELSE 0
                        END
                    ),
                    0
                ) as sedang_dipinjam
            ")
            ->selectSub(function ($sub) {
                $sub->from('laporan_kerusakan as lk')
                    ->join(
                        'peminjaman_detail as pd_lk',
                        'pd_lk.id',
                        '=',
                        'lk.peminjaman_detail_id'
                    )
                    ->whereColumn(
                        'pd_lk.barang_tu_id',
                        'barang_tu.id'
                    )
                    ->selectRaw('COUNT(*)');
            }, 'jumlah_laporan')
            ->groupBy(
                'barang_tu.id',
                'barang_tu.kode_barang',
                'barang_tu.nama_barang',
                'barang_tu.jenis_barang'
            )
            ->orderByDesc('total_transaksi')
            ->orderBy('barang_tu.nama_barang')
            ->get();

        // Jumlah pengajuan yang menunggu verifikasi
        $menungguVerifikasi = Peminjaman::where(
            'status',
            'menunggu'
        )->count();

        // Jumlah laporan kerusakan yang belum selesai
        $jumlahKerusakanAktif = LaporanKerusakan::whereIn(
            'status',
            ['menunggu', 'diproses']
        )->count();

        return view(
            'admin.riwayat.index',
            compact(
                'peminjaman',
                'rekapBarang',
                'totalTransaksi',
                'totalDikembalikan',
                'totalDitolak',
                'totalLaporan',
                'menungguVerifikasi',
                'jumlahKerusakanAktif'
            )
        );
    }
}
