<?php

namespace App\Http\Controllers;

use App\Models\BarangTu;
use App\Models\InventarisKerusakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Barryvdh\DomPDF\Facade\Pdf;

class InventarisKerusakanController extends Controller
{
    /**
     * Daftar inventaris barang rusak.
     */
    public function index(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin_tu', 403);

        $query = InventarisKerusakan::with([
            'barang',
            'admin',
        ])->latest();

        if ($request->filled('search')) {
            $keyword = $request->search;

            $query->where(function ($q) use ($keyword) {
                $q->where('catatan', 'like', "%{$keyword}%")
                    ->orWhereHas('barang', function ($barang) use ($keyword) {
                        $barang->where('nama_barang', 'like', "%{$keyword}%")
                            ->orWhere('kode_barang', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('tingkat_kerusakan')) {
            $query->where(
                'tingkat_kerusakan',
                $request->tingkat_kerusakan
            );
        }

        if ($request->filled('status_penanganan')) {
            $query->where(
                'status_penanganan',
                $request->status_penanganan
            );
        }

        $inventarisRusak = $query->paginate(10)->withQueryString();

        $totalRusak = InventarisKerusakan::whereIn(
            'status_penanganan',
            ['belum_diperbaiki', 'sedang_diperbaiki']
        )->count();

        $rusakRingan = InventarisKerusakan::where(
            'tingkat_kerusakan',
            'rusak_ringan'
        )->whereIn('status_penanganan', [
            'belum_diperbaiki',
            'sedang_diperbaiki',
        ])->count();

        $rusakBerat = InventarisKerusakan::where(
            'tingkat_kerusakan',
            'rusak_berat'
        )->whereIn('status_penanganan', [
            'belum_diperbaiki',
            'sedang_diperbaiki',
        ])->count();

        return view('admin.kerusakan.inventaris', compact(
            'inventarisRusak',
            'totalRusak',
            'rusakRingan',
            'rusakBerat'
        ));
    }

    /**
     * Form tambah barang rusak.
     */
    public function create()
    {
        abort_unless(auth()->user()->role === 'admin_tu', 403);

        $barang = BarangTu::where('status', '!=', 'dipinjam')
            ->whereDoesntHave('inventarisKerusakan', function ($query) {
                $query->whereIn('status_penanganan', [
                    'belum_diperbaiki',
                    'sedang_diperbaiki',
                ]);
            })
            ->orderBy('nama_barang')
            ->get();

        return view('admin.kerusakan.inventaris-create', compact('barang'));
    }

    /**
     * Menyimpan pencatatan barang rusak.
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin_tu', 403);

        $data = $request->validate([
            'barang_tu_id' => [
                'required',
                'integer',
                'exists:barang_tu,id',
            ],

            'tanggal_kerusakan' => [
                'required',
                'date',
            ],

            'tingkat_kerusakan' => [
                'required',
                Rule::in(['rusak_ringan', 'rusak_berat']),
            ],

            'catatan' => [
                'required',
                'string',
                'max:5000',
            ],

            'foto_bukti' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:5120',
            ],
        ]);

        $fotoPath = $request->file('foto_bukti')
            ->store('inventaris-rusak', 'public');

        try {
            DB::transaction(function () use ($data, $fotoPath) {

                $barang = BarangTu::whereKey($data['barang_tu_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($barang->status === 'dipinjam') {
                    throw ValidationException::withMessages([
                        'barang_tu_id' =>
                            'Barang sedang dipinjam. Gunakan laporan kerusakan dari peminjam.',
                    ]);
                }

                $sudahDicatat = InventarisKerusakan::where(
                    'barang_tu_id',
                    $barang->id
                )->whereIn('status_penanganan', [
                    'belum_diperbaiki',
                    'sedang_diperbaiki',
                ])->exists();

                if ($sudahDicatat) {
                    throw ValidationException::withMessages([
                        'barang_tu_id' =>
                            'Barang ini masih memiliki pencatatan kerusakan yang belum selesai.',
                    ]);
                }

                InventarisKerusakan::create([
                    'barang_tu_id' => $barang->id,
                    'created_by' => auth()->id(),
                    'tanggal_kerusakan' => $data['tanggal_kerusakan'],
                    'tingkat_kerusakan' => $data['tingkat_kerusakan'],
                    'catatan' => $data['catatan'],
                    'foto_bukti' => $fotoPath,
                    'status_penanganan' => 'belum_diperbaiki',
                ]);

                // Otomatis memperbarui inventaris utama.
                $barang->update([
                    'kondisi' => $data['tingkat_kerusakan'],
                    'status' => 'tidak_tersedia',
                ]);
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($fotoPath);
            throw $e;
        }

        return redirect()
            ->route('admin.kerusakan.inventaris.index')
            ->with('success', 'Data barang rusak berhasil dicatat. Status inventaris otomatis diperbarui.');
    }

/**
 * Memperbarui status penanganan inventaris rusak.
 */

public function updatePenanganan(
    Request $request,
    InventarisKerusakan $inventarisKerusakan
) {
    abort_unless(auth()->user()->role === 'admin_tu', 403);

    $data = $request->validate([
        'status_penanganan' => [
            'required',
            Rule::in([
                'belum_diperbaiki',
                'sedang_diperbaiki',
                'selesai',
            ]),
        ],

        'tindakan_perbaikan' => [
            'nullable',
            'string',
            'max:5000',
        ],

        'hasil_perbaikan' => [
            'nullable',
            'string',
            'max:5000',
        ],

        'foto_perbaikan' => [
            'nullable',
            'required_if:status_penanganan,selesai',
            'image',
            'mimes:jpg,jpeg,png',
            'max:5120',
        ],
    ], [
        'foto_perbaikan.required_if' =>
            'Foto setelah perbaikan wajib diunggah ketika status selesai.',

        'foto_perbaikan.max' =>
            'Ukuran foto maksimal 5 MB.',
    ]);

    if ($inventarisKerusakan->status_penanganan === 'selesai') {
        return back()->with(
            'error',
            'Barang ini sudah selesai diperbaiki.'
        );
    }

    $fotoPath = null;

    if ($request->hasFile('foto_perbaikan')) {
        $fotoPath = $request->file('foto_perbaikan')
            ->store('foto-perbaikan', 'public');
    }

    try {
        DB::transaction(function () use (
            $inventarisKerusakan,
            $data,
            $fotoPath
        ) {
            $laporan = InventarisKerusakan::whereKey(
                $inventarisKerusakan->id
            )->lockForUpdate()->firstOrFail();

            if ($laporan->status_penanganan === 'selesai') {
                throw new \RuntimeException(
                    'Barang sudah selesai diperbaiki.'
                );
            }

            $laporan->update([
                'status_penanganan' => $data['status_penanganan'],

                'tindakan_perbaikan' =>
                    $data['tindakan_perbaikan'] ?? null,

                'hasil_perbaikan' =>
                    $data['hasil_perbaikan'] ?? null,

                'foto_perbaikan' => $fotoPath
                    ?? $laporan->foto_perbaikan,

                'tanggal_perbaikan' =>
                    $data['status_penanganan'] === 'selesai'
                        ? now()->toDateString()
                        : null,
            ]);

            $barang = BarangTu::whereKey(
                $laporan->barang_tu_id
            )->lockForUpdate()->firstOrFail();

            if ($data['status_penanganan'] === 'selesai') {

                $barang->update([
                    'kondisi' => 'baik',
                    'status' => 'tersedia',
                ]);

            } else {

                $barang->update([
                    'status' => 'tidak_tersedia',
                ]);
            }
        });

    } catch (\Throwable $e) {

        if ($fotoPath) {
            Storage::disk('public')->delete($fotoPath);
        }

        throw $e;
    }

    return back()->with(
        'success',
        $data['status_penanganan'] === 'selesai'
            ? 'Perbaikan selesai. Inventaris sekarang tersedia untuk dipinjam kembali.'
            : 'Status penanganan berhasil diperbarui.'
    );
}


/**
 * EXPORT PDF LAPORAN KERUSAKAN
 * Hanya mengambil data inventaris yang dicatat rusak.
 */
public function exportLaporanKerusakan()
{
    abort_unless(auth()->user()->role === 'admin_tu', 403);

    $laporan = InventarisKerusakan::with([
        'barang',
        'admin',
    ])
    ->latest('tanggal_kerusakan')
    ->get();

    $pdf = Pdf::loadView(
        'admin.kerusakan.pdf.laporan-kerusakan',
        [
            'laporan' => $laporan,
            'tanggalCetak' => now(),
            'namaAdmin' => auth()->user()->nama,
            'namaSekolah' => config('app.school_name', 'Nama Sekolah'),
            'nomorDokumen' => 'LK-' . now()->format('Ymd-His'),
        ]
    );

    $pdf->setPaper('a4', 'portrait');

    return $pdf->download(
        'Laporan-Kerusakan-SISKO-' . now()->format('Ymd') . '.pdf'
    );
}


/**
 * EXPORT PDF LAPORAN PENANGANAN
 * Mengambil semua barang dalam inventaris kerusakan,
 * termasuk yang belum, sedang, dan selesai diperbaiki.
 */
public function exportLaporanPenanganan()
{
    abort_unless(auth()->user()->role === 'admin_tu', 403);

    $laporan = InventarisKerusakan::with([
        'barang',
        'admin',
    ])
    ->latest('tanggal_kerusakan')
    ->get();

    $pdf = Pdf::loadView(
        'admin.kerusakan.pdf.laporan-penanganan',
        [
            'laporan' => $laporan,
            'tanggalCetak' => now(),
            'namaAdmin' => auth()->user()->nama,
            'namaSekolah' => config('app.school_name', 'Nama Sekolah'),
            'nomorDokumen' => 'PN-' . now()->format('Ymd-His'),
        ]
    );

    $pdf->setPaper('a4', 'portrait');

    return $pdf->download(
        'Laporan-Penanganan-SISKO-' . now()->format('Ymd') . '.pdf'
    );
}


    /**
     * Download Excel inventaris barang rusak.
     */
    public function exportExcel(Request $request)
    {
        abort_unless(auth()->user()->role === 'admin_tu', 403);

        $query = InventarisKerusakan::with([
            'barang',
            'admin',
        ])->latest();

        if ($request->filled('search')) {
            $keyword = $request->search;

            $query->where(function ($q) use ($keyword) {
                $q->where('catatan', 'like', "%{$keyword}%")
                    ->orWhereHas('barang', function ($barang) use ($keyword) {
                        $barang->where('nama_barang', 'like', "%{$keyword}%")
                            ->orWhere('kode_barang', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('tingkat_kerusakan')) {
            $query->where(
                'tingkat_kerusakan',
                $request->tingkat_kerusakan
            );
        }

        if ($request->filled('status_penanganan')) {
            $query->where(
                'status_penanganan',
                $request->status_penanganan
            );
        }

        $dataRusak = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventaris Barang Rusak');

        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue(
            'A1',
            'DATA INVENTARIS BARANG RUSAK - SISKO'
        );

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2D684F'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(32);

        $headers = [
            'No.',
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Tingkat Kerusakan',
            'Catatan Kerusakan',
            'Tanggal Kerusakan',
            'Status Penanganan',
            'Bukti Foto',
        ];

        $sheet->fromArray($headers, null, 'A2');

        $sheet->getStyle('A2:I2')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2D684F'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        $sheet->getRowDimension(2)->setRowHeight(35);

        $row = 3;

        foreach ($dataRusak as $index => $item) {
            $barang = $item->barang;

            $kategori = $barang?->jenis_barang
                ? ucwords(str_replace('_', ' ', $barang->jenis_barang))
                : '-';

            $tingkat = $item->tingkat_kerusakan === 'rusak_berat'
                ? 'Rusak Berat'
                : 'Rusak Ringan';

            $status = ucwords(str_replace(
                '_',
                ' ',
                $item->status_penanganan
            ));

            $sheet->fromArray([
                $index + 1,
                $barang?->kode_barang ?? '-',
                $barang?->nama_barang ?? 'Barang tidak ditemukan',
                $kategori,
                $tingkat,
                $item->catatan,
                $item->tanggal_kerusakan?->format('d-m-Y') ?? '-',
                $status,
                '',
            ], null, 'A' . $row);

            $sheet->getRowDimension($row)->setRowHeight(70);

            if ($item->foto_bukti) {
                $fotoPath = Storage::disk('public')->path(
                    $item->foto_bukti
                );

                if (is_file($fotoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Foto Kerusakan');
                    $drawing->setDescription('Bukti foto kerusakan barang');
                    $drawing->setPath($fotoPath);
                    $drawing->setCoordinates('I' . $row);
                    $drawing->setHeight(62);
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(4);
                    $drawing->setWorksheet($sheet);
                } else {
                    $sheet->setCellValue('I' . $row, 'Foto tidak ditemukan');
                }
            } else {
                $sheet->setCellValue('I' . $row, 'Tidak ada foto');
            }

            $row++;
        }

        $lastRow = max(2, $row - 1);

        $widths = [
            'A' => 7,
            'B' => 18,
            'C' => 30,
            'D' => 20,
            'E' => 20,
            'F' => 45,
            'G' => 18,
            'H' => 22,
            'I' => 22,
        ];

        foreach ($widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        $sheet->getStyle('A2:I' . $lastRow)->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DCE5DF'],
                ],
            ],
        ]);

        $sheet->freezePane('A3');
        $sheet->setAutoFilter('A2:I' . $lastRow);

        $fileName = 'Inventaris-Barang-Rusak-SISKO-' .
            now()->format('Ymd-His') . '.xlsx';

        $tempFile = tempnam(sys_get_temp_dir(), 'sisko_rusak_');

        try {
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempFile);
        } catch (\Throwable $e) {
            if (is_file($tempFile)) {
                unlink($tempFile);
            }

            throw $e;
        }

        return response()
            ->download($tempFile, $fileName)
            ->deleteFileAfterSend(true);
    }
}
