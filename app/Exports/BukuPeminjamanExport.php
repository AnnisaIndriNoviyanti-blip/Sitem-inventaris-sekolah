<?php

namespace App\Exports;

use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BukuPeminjamanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths
{
    protected Collection $peminjaman;

    public function __construct(Collection $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    public function collection(): Collection
    {
        return $this->peminjaman;
    }

    public function headings(): array
    {
        return [
            'NAMA',
            'NO / KODE BARANG',
            'LCD',
            'KABEL',
            'SPEAKER',
            'LAPTOP',
            'TANGGAL PEMINJAMAN',
            'TANGGAL PENGEMBALIAN',
            'TANDA TANGAN',
        ];
    }

    public function map($item): array
    {
        $details = $item->detail->filter(
            fn ($detail) => $detail->barang !== null
        );

        $kategori = fn ($barang) => $this->kategoriBarang($barang);

        $paket = $details->filter(
            fn ($detail) =>
                $kategori($detail->barang) === 'paket_proyektor'
        );

        $speaker = $details->filter(
            fn ($detail) =>
                $kategori($detail->barang) === 'speaker'
        );

        $laptop = $details->filter(
            fn ($detail) =>
                $kategori($detail->barang) === 'laptop'
        );

        $kode = $details
            ->pluck('barang.kode_barang')
            ->filter()
            ->implode(', ');

        $tanggalKembali = $item->tanggal_kembali
            ?? $item->ttd_pengembalian_at;

        return [
            $item->nama_peminjam
                ?? $item->user?->nama
                ?? '-',

            $kode ?: '-',

            $paket->isNotEmpty() ? '✓' : '',
            $paket->isNotEmpty() ? '✓' : '',
            $speaker->isNotEmpty() ? '✓' : '',
            $laptop->isNotEmpty() ? '✓' : '',

            $item->tanggal_pinjam?->format('d/m/Y') ?? '-',

            $tanggalKembali
                ? \Carbon\Carbon::parse($tanggalKembali)->format('d/m/Y')
                : '',

            ($item->ttd_peminjam_data || $item->ttd_peminjam)
                ? 'Sudah ditandatangani'
                : 'Belum ada tanda tangan',
        ];
    }

    private function kategoriBarang($barang): string
    {
        if ($barang->jenis_barang) {
            return strtolower($barang->jenis_barang);
        }

        $nama = strtolower($barang->nama_barang);

        if (str_contains($nama, 'speaker')) {
            return 'speaker';
        }

        if (str_contains($nama, 'laptop')) {
            return 'laptop';
        }

        if (
            str_contains($nama, 'proyektor') ||
            str_contains($nama, 'projector') ||
            str_contains($nama, 'lcd') ||
            str_contains($nama, 'hdmi') ||
            str_contains($nama, 'kabel')
        ) {
            return 'paket_proyektor';
        }

        return 'lainnya';
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $sheet->getStyle('A1:I' . ($this->peminjaman->count() + 1))
            ->getAlignment()
            ->setVertical('center');

        foreach ($this->peminjaman as $index => $item) {
            $sheet->getRowDimension($index + 2)->setRowHeight(22);
        }

        return [
            1 => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                    'wrapText' => true,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 10,
            'D' => 10,
            'E' => 12,
            'F' => 12,
            'G' => 20,
            'H' => 22,
            'I' => 20,
        ];
    }
}
