<?php

namespace App\Http\Controllers;

use App\Models\LaporanKerusakan;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanKerusakanController extends Controller
{
    /**
     * Daftar laporan kerusakan.
     * Admin TU melihat semua laporan.
     * Guru dan siswa hanya melihat laporan miliknya.
     */
    public function index()
{
    $user = auth()->user();

    // Admin TU diarahkan ke halaman pengelolaan laporan
    if ($user->role === 'admin_tu') {
        return redirect()->route('admin.kerusakan.index');
    }

    // Guru dan siswa hanya melihat laporan miliknya
    $laporan = LaporanKerusakan::with([
        'peminjaman.user',
        'peminjamanDetail.barang',
    ])
        ->whereHas('peminjaman', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->latest()
        ->paginate(10);

    return view('laporan-kerusakan.index', compact('laporan'));
}

    /**
     * Form pembuatan laporan kerusakan.
     */
    public function create(Peminjaman $peminjaman)
    {
        abort_unless(
            in_array(auth()->user()->role, ['guru', 'siswa'], true),
            403
        );

        abort_unless(
            $peminjaman->user_id == auth()->id(),
            403,
            'Anda tidak memiliki akses ke peminjaman ini.'
        );

        abort_unless(
            in_array(
                $peminjaman->status,
                ['dipinjam', 'dikembalikan'],
                true
            ),
            403,
            'Laporan hanya dapat dibuat untuk peminjaman yang sudah disetujui.'
        );

        $peminjaman->load('detail.barang');

        return view(
            'laporan-kerusakan.create',
            compact('peminjaman')
        );
    }

    /**
     * Menyimpan laporan kerusakan.
     */
    public function store(Request $request, Peminjaman $peminjaman)
    {
        abort_unless(
            in_array(auth()->user()->role, ['guru', 'siswa'], true),
            403
        );

        abort_unless(
            $peminjaman->user_id == auth()->id(),
            403,
            'Anda tidak memiliki akses ke peminjaman ini.'
        );

        abort_unless(
            in_array(
                $peminjaman->status,
                ['dipinjam', 'dikembalikan'],
                true
            ),
            403,
            'Laporan tidak dapat dibuat untuk status peminjaman ini.'
        );

        
$data = $request->validate([
    'peminjaman_detail_id' => [
        'required',
        'integer',
        Rule::exists('peminjaman_detail', 'id')
            ->where('peminjaman_id', $peminjaman->id),
    ],

    'jenis_laporan' => [
        'required',
        Rule::in([
            'saat_diterima',
            'selama_dipinjam',
        ]),
    ],

    'tanggal_laporan' => ['required', 'date'],

    'nomor_whatsapp' => [
        'required',
        'string',
        'max:20',
        'regex:/^[0-9+\-\s()]+$/',
    ],

    'keterangan' => [
        'required',
        'string',
        'max:5000',
    ],

    'bukti_laporan' => [
        'required',
        'image',
        'mimes:jpg,jpeg,png',
        'max:5120',
    ],
]);


        $path = $request->file('bukti_laporan')
            ->store('bukti-kerusakan', 'public');

        try {
            DB::transaction(function () use (
                $peminjaman,
                $data,
                $path
            ) {
                LaporanKerusakan::create([
                    'peminjaman_id' => $peminjaman->id,

                    'peminjaman_detail_id' =>
                        $data['peminjaman_detail_id'] ?? null,

                    'tanggal_laporan' => $data['tanggal_laporan'],
                    'nomor_whatsapp' => $data['nomor_whatsapp'],
                    'keterangan' => $data['keterangan'],
                    'jenis_laporan' => $data['jenis_laporan'],
                    'bukti_laporan' => $path,
                    'status' => 'menunggu',
                    
                ]);
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);

            throw $e;
        }

        return redirect()
            ->route('laporan-kerusakan.index')
            ->with(
                'success',
                'Laporan kerusakan berhasil dikirim kepada admin TU.'
            );
    }

    /**
     * Admin TU memperbarui status laporan kerusakan.
     */
    public function updateStatus(
        Request $request,
        LaporanKerusakan $laporanKerusakan
    ) {
        abort_unless(
            auth()->user()->role === 'admin_tu',
            403
        );

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in(['diproses', 'selesai', 'ditolak']),
            ],

            'catatan_admin' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        if (
            in_array(
                $laporanKerusakan->status,
                ['selesai', 'ditolak'],
                true
            )
        ) {
            return back()->with(
                'error',
                'Laporan yang sudah selesai atau ditolak tidak dapat diubah.'
            );
        }

        $laporanKerusakan->update([
            'status' => $data['status'],
            'catatan_admin' => $data['catatan_admin'] ?? null,
        ]);

        return back()->with(
            'success',
            'Status dan catatan laporan berhasil diperbarui.'
        );
    }

    
public function adminIndex(Request $request)
{
    abort_unless(
        auth()->user()->role === 'admin_tu',
        403
    );

    $query = LaporanKerusakan::with([
        'peminjaman.user',
        'peminjamanDetail.barang',
    ])->latest();

    // Pencarian laporan
    $cari = $request->input(
        'search',
        $request->input('cari')
    );

    if ($cari) {
        $query->where(function ($q) use ($cari) {

            $q->where('nomor_whatsapp', 'like', "%{$cari}%")
                ->orWhere('keterangan', 'like', "%{$cari}%")
                ->orWhereHas('peminjaman.user', function ($user) use ($cari) {
                    $user->where('nama', 'like', "%{$cari}%");
                })
                ->orWhereHas('peminjamanDetail.barang', function ($barang) use ($cari) {
                    $barang->where('nama_barang', 'like', "%{$cari}%");
                });

        });
    }

    // Filter status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter tingkat / jenis laporan
    if ($request->filled('jenis_laporan')) {
        $query->where(
            'jenis_laporan',
            $request->jenis_laporan
        );
    }

    // Pagination
    $laporan = $query->paginate(10)->withQueryString();

    // Statistik keseluruhan, tidak hanya halaman aktif
    $jumlahKerusakan = LaporanKerusakan::whereIn(
        'status',
        ['menunggu', 'diproses']
    )->count();

    $jumlahMenunggu = LaporanKerusakan::where(
        'status',
        'menunggu'
    )->count();

    $jumlahDiproses = LaporanKerusakan::where(
        'status',
        'diproses'
    )->count();

    $jumlahSelesai = LaporanKerusakan::where(
        'status',
        'selesai'
    )->count();

    $totalLaporan = LaporanKerusakan::count();

    return view('admin.kerusakan.index', compact(
        'laporan',
        'jumlahKerusakan',
        'jumlahMenunggu',
        'jumlahDiproses',
        'jumlahSelesai',
        'totalLaporan'
    ));
}
public function exportExcel(Request $request)
{
    abort_unless(
        auth()->user()->role === 'admin_tu',
        403
    );

    // Mengambil seluruh laporan beserta relasinya
    $query = LaporanKerusakan::with([
        'peminjaman.user',
        'peminjamanDetail.barang',
    ])->latest();

    // Filter pencarian
    $cari = $request->input(
        'search',
        $request->input('cari')
    );

    if ($cari) {
        $query->where(function ($q) use ($cari) {

            $q->where('nomor_whatsapp', 'like', "%{$cari}%")
                ->orWhere('keterangan', 'like', "%{$cari}%")
                ->orWhereHas('peminjaman.user', function ($user) use ($cari) {
                    $user->where('nama', 'like', "%{$cari}%");
                })
                ->orWhereHas('peminjamanDetail.barang', function ($barang) use ($cari) {
                    $barang->where('nama_barang', 'like', "%{$cari}%");
                });

        });
    }

    // Filter status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter jenis laporan
    if ($request->filled('jenis_laporan')) {
        $query->where(
            'jenis_laporan',
            $request->jenis_laporan
        );
    }

    // Mengambil seluruh hasil, tanpa pagination
    $laporan = $query->get();

    // Membuat spreadsheet
    $spreadsheet = new Spreadsheet();

    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setTitle('Laporan Kerusakan');

    // Judul tabel
    $sheet->mergeCells('A1:M1');

    $sheet->setCellValue(
        'A1',
        'REKAPITULASI LAPORAN KERUSAKAN BARANG - SISKO'
    );

    $sheet->getStyle('A1')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 14,
            'color' => [
                'rgb' => 'FFFFFF'
            ],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => [
                'rgb' => '2D684F'
            ],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
    ]);

    $sheet->getRowDimension(1)->setRowHeight(32);

    // Header tabel
    $headers = [
        'No.',
        'Kode Laporan',
        'Nama Barang',
        'Kode Peminjaman',
        'Nama Pelapor',
        'Jumlah Unit Rusak',
        'Keterangan Kerusakan',
        'Jenis Laporan',
        'Tingkat Kerusakan',
        'Status',
        'Tanggal Laporan',
        'Nomor WhatsApp',
        'Bukti Foto',
    ];

    $sheet->fromArray(
        $headers,
        null,
        'A2'
    );

    // Styling header
    $sheet->getStyle('A2:M2')->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => [
                'rgb' => 'FFFFFF'
            ],
            'size' => 10,
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => [
                'rgb' => '2D684F'
            ],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ],
    ]);

    $sheet->getRowDimension(2)->setRowHeight(35);

    // Mengisi data laporan
    $row = 3;

    foreach ($laporan as $index => $item) {

        // Nama barang
        $namaBarang = $item->peminjamanDetail?->barang?->nama_barang
            ?? 'Barang tidak ditemukan';

        // Kode peminjaman
        $kodePeminjaman = $item->kode_peminjaman
            ?? 'PJ-' . str_pad(
                (string) ($item->peminjaman_id ?? 0),
                6,
                '0',
                STR_PAD_LEFT
            );

        // Nama pelapor
        $namaPelapor = $item->peminjaman?->user?->nama
            ?? $item->peminjaman?->user?->name
            ?? 'Peminjam';

        // Jumlah barang rusak
        $jumlahRusak = $item->peminjamanDetail?->jumlah ?? 1;

        // Kode laporan
        $kodeLaporan = $item->kode_laporan
            ?? 'LK-' .
            optional($item->tanggal_laporan)->format('ymd') .
            '-' .
            str_pad(
                (string) $item->id,
                2,
                '0',
                STR_PAD_LEFT
            );

        // Tanggal tanpa jam
        $tanggal = $item->tanggal_laporan
            ? \Carbon\Carbon::parse(
                $item->tanggal_laporan
            )->format('d-m-Y')
            : '-';

        // Jenis laporan
        $jenisLaporan = [
            'saat_diterima' => 'Saat diterima',
            'selama_dipinjam' => 'Selama dipinjam',
        ][$item->jenis_laporan] ?? '-';

        // Status
        $status = ucfirst($item->status);

        // Mengisi data ke Excel
        $sheet->fromArray([
            $index + 1,
            $kodeLaporan,
            $namaBarang,
            $kodePeminjaman,
            $namaPelapor,
            $jumlahRusak,
            $item->keterangan ?? '-',
            $jenisLaporan,
            ucfirst($item->tingkat_kerusakan ?? '-'),
            $status,
            $tanggal,
            $item->nomor_whatsapp ?? '-',
            '',
        ], null, 'A' . $row);

        // Mengatur tinggi baris
        $sheet->getRowDimension($row)->setRowHeight(65);

        // Mengambil bukti foto dari storage
        if ($item->bukti_laporan) {

            $fotoPath = Storage::disk('public')->path(
                $item->bukti_laporan
            );

            if (is_file($fotoPath)) {

                $drawing = new Drawing();

                $drawing->setName('Bukti Kerusakan');

                $drawing->setDescription(
                    'Foto bukti laporan kerusakan'
                );

                $drawing->setPath($fotoPath);

                $drawing->setCoordinates('M' . $row);

                $drawing->setHeight(60);

                $drawing->setOffsetX(5);

                $drawing->setOffsetY(3);

                $drawing->setWorksheet($sheet);

            } else {

                $sheet->setCellValue(
                    'M' . $row,
                    'File foto tidak ditemukan'
                );

            }

        } else {

            $sheet->setCellValue(
                'M' . $row,
                'Tidak ada foto'
            );

        }

        $row++;
    }

    // Mengatur lebar kolom
    $columnWidths = [
        'A' => 7,
        'B' => 20,
        'C' => 30,
        'D' => 20,
        'E' => 25,
        'F' => 16,
        'G' => 45,
        'H' => 20,
        'I' => 20,
        'J' => 15,
        'K' => 17,
        'L' => 20,
        'M' => 22,
    ];

    foreach ($columnWidths as $column => $width) {
        $sheet->getColumnDimension($column)
            ->setWidth($width);
    }

    // Styling seluruh tabel
    $lastRow = max(2, $row - 1);

    $sheet->getStyle(
        'A2:M' . $lastRow
    )->applyFromArray([
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => [
                    'rgb' => 'DCE5DF'
                ],
            ],
        ],
    ]);

    // Posisi header tetap terlihat saat digulir
    $sheet->freezePane('A3');

    // Filter tabel Excel
    $sheet->setAutoFilter(
        'A2:M' . $lastRow
    );

    // Pengaturan halaman
    $sheet->getPageSetup()
        ->setOrientation(
            \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
        );

    $sheet->getPageSetup()
        ->setFitToWidth(1);

    $sheet->getPageSetup()
        ->setFitToHeight(0);

    $sheet->getPageSetup()
        ->setFitToPage(true);

    // Membuat file sementara
    $fileName = 'Laporan-Kerusakan-SISKO-' .
        now()->format('Ymd-His') . '.xlsx';

    $tempFile = tempnam(
        sys_get_temp_dir(),
        'sisko_kerusakan_'
    );

    try {

        $writer = new Xlsx($spreadsheet);

        $writer->save($tempFile);

    } catch (\Throwable $e) {

        if (is_file($tempFile)) {
            unlink($tempFile);
        }

        throw $e;

    }

    // Download file Excel
    return response()
        ->download($tempFile, $fileName)
        ->deleteFileAfterSend(true);
}

}