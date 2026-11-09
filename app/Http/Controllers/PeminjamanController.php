<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangTu;
use App\Models\Peminjaman;
use App\Models\PeminjamanDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Exports\BukuPeminjamanExport;
use Maatwebsite\Excel\Facades\Excel;

class PeminjamanController extends Controller
{
    public function index()
{
    $user = auth()->user();

    $query = Peminjaman::with([
        'user',
        'detail.barang',
    ])->latest();

    if ($user->role !== 'admin_tu') {
        $query->where('user_id', $user->id);
    }

    $peminjaman = $query->paginate(10);

    $barang = BarangTu::query()
        ->where('status', 'tersedia')
        ->where('kondisi', 'baik')
        ->orderBy('nama_barang')
        ->get();

    return view('peminjaman.index', compact(
        'peminjaman',
        'barang'
    ));
}

public function katalog(Request $request)
{
    abort_unless(
        in_array(auth()->user()->role, ['guru', 'siswa'], true),
        403
    );

    // Query utama untuk menampilkan barang
$query = BarangTu::query();

// Pencarian barang
if ($request->filled('search')) {
    $keyword = trim($request->search);

    $query->where(function ($q) use ($keyword) {
        $q->where('nama_barang', 'like', "%{$keyword}%")
            ->orWhere('kode_barang', 'like', "%{$keyword}%")
            ->orWhere('spesifikasi', 'like', "%{$keyword}%");
    });
}

// Filter kategori
if ($request->filled('kategori')) {
    $query->where('jenis_barang', $request->kategori);
}

// Filter ketersediaan
if ($request->boolean('tersedia')) {
    $query->where('status', 'tersedia')
        ->where('kondisi', 'baik');
}

// Mengambil barang sesuai filter
$barang = $query->orderBy('nama_barang')
    ->paginate(9)
    ->withQueryString();

// Mengambil SEMUA kategori dari database,
// tidak terpengaruh filter kategori yang sedang dipilih.
$kategori = BarangTu::query()
    ->whereNotNull('jenis_barang')
    ->whereRaw("TRIM(jenis_barang) != ''")
    ->select('jenis_barang')
    ->distinct()
    ->orderBy('jenis_barang')
    ->pluck('jenis_barang');

// Menghitung seluruh jenis barang
$totalBarang = BarangTu::count();

$barangTersedia = BarangTu::where('status', 'tersedia')
    ->where('kondisi', 'baik')
    ->count();

return view('peminjaman.katalog', compact(
    'barang',
    'totalBarang',
    'barangTersedia',
    'kategori'
));
}
    public function create(Request $request)
{
    $barang = BarangTu::where('status', 'tersedia')
        ->where('kondisi', 'baik')
        ->get();

    // Mengambil ID barang dari halaman katalog
    $barangTerpilih = $request->query('barang_id');

    return view('peminjaman.create', compact(
        'barang',
        'barangTerpilih'
    ));
}

public function store(Request $request)
{
    $user = auth()->user();

    abort_unless(
        in_array($user->role, ['guru', 'siswa'], true),
        403
    );

    $aturan = [
        'tanggal_pinjam' => [
            'required',
            'date',
            'after_or_equal:today',
        ],
        'tanggal_rencana_kembali' => [
            'required',
            'date',
            'after_or_equal:tanggal_pinjam',
        ],
        'keperluan' => ['required', 'string', 'max:1000'],
        'catatan' => ['nullable', 'string', 'max:2000'],
        'ttd_peminjam_data' => [
            'required',
            'string',
            'max:200000',
        ],
        'barang' => ['required', 'array', 'min:1'],
        'barang.*.barang_tu_id' => [
            'required',
            'integer',
            'distinct',
            'exists:barang_tu,id',
        ],
        'barang.*.jumlah' => [
            'required',
            'integer',
            'in:1',
        ],
    ];

    
if (in_array($user->jenis_akun, ['kelas', 'organisasi'], true)) {
    $aturan['nama_peminjam'] = [
        'required',
        'string',
        'max:255',
    ];
}


    $data = $request->validate($aturan, [
        'tanggal_pinjam.after_or_equal' =>
            'Tanggal peminjaman tidak boleh sebelum hari ini.',
        'tanggal_rencana_kembali.after_or_equal' =>
            'Rencana pengembalian tidak boleh sebelum tanggal peminjaman.',
        'nama_peminjam.required' =>
            'Nama guru penanggung jawab wajib diisi.',
        'barang.required' =>
            'Pilih minimal satu barang yang akan dipinjam.',
        'barang.min' =>
            'Pilih minimal satu barang yang akan dipinjam.',
    ]);

    
$namaPeminjam = in_array(
    $user->jenis_akun,
    ['kelas', 'organisasi'],
    true
)
    ? trim($data['nama_peminjam'])
    : $user->nama;


    
$signatureData = json_decode($data['ttd_peminjam_data'], true);

// Pastikan payload tanda tangan berisi koordinat canvas yang valid.
if (
    !is_array($signatureData) ||
    ($signatureData['width'] ?? null) !== 900 ||
    ($signatureData['height'] ?? null) !== 260 ||
    !isset($signatureData['strokes']) ||
    !is_array($signatureData['strokes']) ||
    count($signatureData['strokes']) === 0 ||
    count($signatureData['strokes']) > 100
) {
    throw ValidationException::withMessages([
        'ttd_peminjam_data' => 'Data tanda tangan tidak valid. Silakan gambar ulang.',
    ]);
}

$jumlahTitik = 0;

foreach ($signatureData['strokes'] as $stroke) {
    if (!is_array($stroke) || count($stroke) === 0) {
        throw ValidationException::withMessages([
            'ttd_peminjam_data' => 'Goresan tanda tangan tidak valid.',
        ]);
    }

    foreach ($stroke as $point) {
        $jumlahTitik++;

        if (
            !is_array($point) ||
            !isset($point['x'], $point['y']) ||
            !is_numeric($point['x']) ||
            !is_numeric($point['y']) ||
            (float) $point['x'] < 0 ||
            (float) $point['x'] > 900 ||
            (float) $point['y'] < 0 ||
            (float) $point['y'] > 260 ||
            $jumlahTitik > 5000
        ) {
            throw ValidationException::withMessages([
                'ttd_peminjam_data' => 'Koordinat tanda tangan tidak valid atau terlalu banyak.',
            ]);
        }
    }
}



    try {
        DB::transaction(function () use (
            $data,
            $signatureData,
            $namaPeminjam,
            $user
        ) {
            $barangIds = collect($data['barang'])
                ->pluck('barang_tu_id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();

            $barangTersedia = BarangTu::whereIn('id', $barangIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($barangTersedia->count() !== $barangIds->unique()->count()) {
                throw ValidationException::withMessages([
                    'barang' => 'Ada barang yang tidak ditemukan.',
                ]);
            }

            foreach ($barangIds as $id) {
                $barang = $barangTersedia->get($id);

                if (
                    !$barang ||
                    $barang->status !== 'tersedia' ||
                    $barang->kondisi !== 'baik'
                ) {
                    throw ValidationException::withMessages([
                        'barang' =>
                            'Ada barang yang tidak tersedia atau kondisinya tidak baik.',
                    ]);
                }
            }

            $peminjaman = Peminjaman::create([
    'user_id' => $user->id,
    'nama_peminjam' => $namaPeminjam,
    'tanggal_pinjam' => $data['tanggal_pinjam'],
    'tanggal_rencana_kembali' =>
        $data['tanggal_rencana_kembali'],
    'tanggal_kembali' => null,
    'keperluan' => $data['keperluan'],
    'catatan' => $data['catatan'] ?? null,
    'status' => 'menunggu',

    // Menyimpan data koordinat tanda tangan
    'ttd_peminjam_data' => $signatureData,

    // Mencatat waktu tanda tangan
    'ttd_peminjaman_at' => now(),
]);

            foreach ($data['barang'] as $item) {
                PeminjamanDetail::create([
                    'peminjaman_id' => $peminjaman->id,
                    'barang_tu_id' => $item['barang_tu_id'],
                    'jumlah' => 1,
                ]);
            }
        });
    } catch (\Throwable $e) {
        throw $e;
    }

    return redirect()
        ->route('peminjaman.index')
        ->with('success', 'Pengajuan peminjaman berhasil dikirim.');
}


    
public function show(Peminjaman $peminjaman)
{
    abort_unless(
        auth()->user()->role === 'admin_tu' ||
        $peminjaman->user_id === auth()->id(),
        403
    );

    $peminjaman->load([
        'user',
        'detail.barang',
        'laporanKerusakan',
    ]);

    if (request()->ajax()) {
    return view(
        'peminjaman.partials.detail-modal',
        compact('peminjaman')
    );
}
    return view('peminjaman.show', compact('peminjaman'));
}


    public function proses(Request $request, Peminjaman $peminjaman)
    {
        abort_unless(auth()->user()->role === 'admin_tu', 403);

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in(['disetujui', 'ditolak']),
            ],
        ]);

        DB::transaction(function()use ($peminjaman, $data){
            $peminjaman = Peminjaman::whereKey($peminjaman->id)
            ->lockForUpdate()
            ->firstOrFail();

            if ($peminjaman->status !== 'menunggu'){
                throw ValidationException::withMessages([
                    'status' => 'Pengajuan ini sudah diproses.',
                ]);
            }

            if($data['status'] == 'ditolak'){
                $peminjaman->update(['status' => 'ditolak']);
                return;
            }

            $details = $peminjaman->detail()->with('barang')->get();

            if ($details->isEmpty()){
                throw ValidationException::withMessages([
                    'status' => 'Peminjaman belum memiliki detail barang.',
                ]);
            }

            foreach ($details as $detail){
                $barang = BarangTu::whereKey($detail->barang_tu_id)
                ->lockForUpdate()
                ->firstOrFail();

                if (
                    $barang->status !== 'tersedia' ||
                    $barang->kondisi !== 'baik'
                ) {
                    throw ValidationException::withMessages([
                        'status' => "Barang {$barang->nama_barang} tidak tersedia.",
                    ]);
                }
            }

            foreach ($details as $detail) {
                $detail->barang->update(['status' => 'dipinjam']);
            }

            $peminjaman->update([
                'status' => 'dipinjam',
                'ttd_peminjaman_at' => now(),
            ]);
        });

        return back()->with(
            'success',
            $data['status'] === 'ditolak'
            ? 'Pengajuan berhasil ditolak.'
            : 'Peminjaman disetujui dan dokumentaso TTD peminjaman dicatat.'
        );
    }

    public function kembalikan(Peminjaman $peminjaman)
    {
        abort_unless(auth()->user()->role === 'admin_tu', 403);

        DB::transaction(function () use ($peminjaman){
            $peminjaman = Peminjaman::whereKey($peminjaman->id)
            ->lockForUpdate()
            ->firstOrFail();

            if ($peminjaman->status !== 'dipinjam') {
                throw ValidationException::withMessages([
                    'status' => 'Hanya peminjaman aktif yang dapat dikembalikan.',
                ]);
            }

            $details = $peminjaman->detail()->with('barang')->get();

            foreach ($details as $detail) {
                $barang = BarangTu::whereKey($detail->barang_tu_id)
                ->lockForUpdate()
                ->firstOrFail();

                // Barang yang dilaporkan rusak tidak otomatis
                // ditandai tersedia kembali.

                $adaLaporanBelumSelesai = $peminjaman 
                ->laporanKerusakan()
                ->where(function ($query) use ($detail){
                    $query->where(
                        'peminjaman_detail_id',
                        $detail->id
                    );
                })
                ->whereIn('status', ['menunggu', 'diproses'])
                ->exists();
                if (!$adaLaporanBelumSelesai){
                    $barang->update(['status' => 'tersedia']);
                } else {
                    $barang->update(['status' => 'tidak_tersedia']);
                }
            }

            $peminjaman->update([
    'status' => 'dikembalikan',
    'tanggal_kembali' => now()->toDateString(),
    'ttd_pengembalian_at' => now(),
]);
        });

        return back()->with(
            'success',
            'Pengembalian dikonfirmasi. Dokumentasi TTD pengembalian berhasil dicatat.'
        );
    }

    
public function verifikasi(Request $request)
{
    abort_unless(
        auth()->user()->role === 'admin_tu',
        403
    );

    $query = Peminjaman::with([
        'user',
        'detail.barang',
    ])->latest();

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('cari')) {
        $cari = $request->cari;

        $query->where(function ($q) use ($cari) {
            $q->where('nama_peminjam', 'like', "%{$cari}%")
              ->orWhereHas('user', function ($user) use ($cari) {
                  $user->where('nama', 'like', "%{$cari}%");
              })
              ->orWhereHas('detail.barang', function ($barang) use ($cari) {
                  $barang->where(
                      'nama_barang',
                      'like',
                      "%{$cari}%"
                  );
              });
        });
    }

    $peminjaman = $query->paginate(10)
        ->withQueryString();

    $menungguVerifikasi = Peminjaman::where(
        'status',
        'menunggu'
    )->count();

    return view('admin.peminjaman.index', compact(
        'peminjaman',
        'menungguVerifikasi'
    ));
}


public function pengembalianIndex(Request $request)
{
    abort_unless(
        auth()->user()->role === 'admin_tu',
        403
    );

    $query = Peminjaman::with([
        'user.organisasi',
        'detail.barang',
    ])
    ->where('status', 'dikembalikan')
    ->latest();

    if ($request->filled('cari')) {
        $cari = $request->cari;

        $query->where(function ($q) use ($cari) {
            $q->where('nama_peminjam', 'like', "%{$cari}%")
              ->orWhereHas('user', function ($user) use ($cari) {
                  $user->where('nama', 'like', "%{$cari}%");
              })
              ->orWhereHas('detail.barang', function ($barang) use ($cari) {
                  $barang->where(
                      'nama_barang',
                      'like',
                      "%{$cari}%"
                  );
              });
        });
    }

    $peminjaman = $query->paginate(10)
        ->withQueryString();

    $menungguVerifikasi = Peminjaman::where(
        'status',
        'menunggu'
    )->count();

    $jumlahKerusakan = \App\Models\LaporanKerusakan::whereIn(
        'status',
        ['menunggu', 'diproses']
    )->count();

    return view('pengembalian.index', compact(
        'peminjaman',
        'menungguVerifikasi',
        'jumlahKerusakan'
    ));
}

public function cetakBuku()
{
    abort_unless(
        auth()->user()->role === 'admin_tu',
        403
    );

    $peminjaman = Peminjaman::with([
        'user',
        'detail.barang',
    ])
    ->where('status', 'dikembalikan')
    ->orderBy('tanggal_pinjam')
    ->orderBy('id')
    ->get();

    return view(
        'admin.peminjaman.cetak',
        compact('peminjaman')
    );
}


public function exportBuku()
{
    abort_unless(
        auth()->user()->role === 'admin_tu',
        403
    );

    $peminjaman = Peminjaman::with([
        'user',
        'detail.barang',
    ])
    ->where('status', 'dikembalikan')
    ->orderBy('tanggal_pinjam')
    ->orderBy('id')
    ->get();

    return Excel::download(
        new BukuPeminjamanExport($peminjaman),
        'Buku_Peminjaman_SISKO.xlsx'
    );
}
}
