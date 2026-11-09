
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">

<style>
    @page {
        size: A4 portrait;
        margin: 35px 40px;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 10px;
        color: #26382e;
        line-height: 1.6;
    }

    .header {
        text-align: center;
        border-bottom: 3px solid #2d684f;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .header h1 {
        color: #285d46;
        font-size: 18px;
        margin: 0;
    }

    .header h2 {
        font-size: 12px;
        margin: 5px 0;
    }

    .header p {
        font-size: 9px;
        margin: 0;
    }

    .info {
        width: 100%;
        margin-bottom: 18px;
    }

    .info td {
        padding: 4px;
    }

    .item {
        border: 1px solid #dce5df;
        border-radius: 7px;
        padding: 14px;
        margin-bottom: 20px;
        page-break-inside: avoid;
    }

    .item-title {
        color: #285d46;
        font-size: 13px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .data {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 12px;
    }

    .data td {
        border: 1px solid #dce5df;
        padding: 7px;
        vertical-align: top;
    }

    .data td:first-child {
        width: 32%;
        background: #f0f5f1;
        font-weight: bold;
    }

    .status {
        font-weight: bold;
        color: #285d46;
    }

    .photo-heading {
        font-weight: bold;
        color: #285d46;
        margin: 14px 0 8px;
    }

    .photo-grid {
        width: 100%;
        border-collapse: collapse;
    }

    .photo-grid td {
        width: 50%;
        padding: 5px;
        text-align: center;
        vertical-align: top;
    }

    .photo-grid img {
        max-width: 100%;
        max-height: 220px;
    }

    .photo-caption {
        font-size: 9px;
        font-weight: bold;
        margin-bottom: 7px;
    }

    .no-photo {
        background: #f1f4f1;
        padding: 30px 5px;
        color: #777;
    }

    .signature {
        width: 100%;
        margin-top: 40px;
        text-align: center;
    }

    .signature td {
        width: 50%;
    }

    .signature-space {
        height: 65px;
    }

    .footer {
        text-align: center;
        font-size: 8px;
        color: #718174;
        margin-top: 25px;
    }
</style>
</head>

<body>

<div class="header">
    <h1>LAPORAN PENANGANAN KERUSAKAN BARANG</h1>
    <h2>{{ $namaSekolah }}</h2>
    <p>SISKO — Sistem Peminjaman Barang Tata Usaha</p>
</div>

<table class="info">
    <tr>
        <td><strong>Nomor dokumen:</strong></td>
        <td>{{ $nomorDokumen }}</td>
        <td><strong>Tanggal cetak:</strong></td>
        <td>{{ $tanggalCetak->format('d-m-Y') }}</td>
    </tr>

    <tr>
        <td><strong>Penanggung jawab:</strong></td>
        <td colspan="3">{{ $namaAdmin }}</td>
    </tr>
</table>

<h3>DAFTAR PENANGANAN INVENTARIS RUSAK</h3>

<p>
    Dokumen ini memuat seluruh inventaris yang tercatat mengalami
    kerusakan, termasuk barang yang belum diperbaiki, sedang
    diperbaiki, dan telah selesai diperbaiki.
</p>

@if ($laporan->isEmpty())

    <p>Belum terdapat data penanganan inventaris.</p>

@else

    @foreach ($laporan as $index => $item)

        @php
            $fotoAwal = $item->foto_bukti
                ? storage_path('app/public/' . $item->foto_bukti)
                : null;

            $fotoAkhir = $item->foto_perbaikan
                ? storage_path('app/public/' . $item->foto_perbaikan)
                : null;

            $statusLabel = [
                'belum_diperbaiki' => 'Belum Diperbaiki',
                'sedang_diperbaiki' => 'Sedang Diperbaiki',
                'selesai' => 'Selesai Diperbaiki',
            ][$item->status_penanganan] ?? 'Belum Ditentukan';
        @endphp

        <div class="item">

            <div class="item-title">
                {{ $index + 1 }}.
                {{ $item->barang?->nama_barang ?? 'Barang tidak ditemukan' }}
            </div>

            <table class="data">
                <tr>
                    <td>Kode barang</td>
                    <td>{{ $item->barang?->kode_barang ?? '-' }}</td>
                </tr>

                <tr>
                    <td>Tanggal kerusakan</td>
                    <td>
                        {{ $item->tanggal_kerusakan
                            ? \Carbon\Carbon::parse($item->tanggal_kerusakan)->format('d F Y')
                            : '-' }}
                    </td>
                </tr>

                <tr>
                    <td>Tingkat kerusakan</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->tingkat_kerusakan ?? '-')) }}</td>
                </tr>

                <tr>
                    <td>Status penanganan</td>
                    <td class="status">{{ $statusLabel }}</td>
                </tr>

                <tr>
                    <td>Catatan kerusakan</td>
                    <td>{{ $item->catatan ?? '-' }}</td>
                </tr>

                <tr>
                    <td>Tindakan perbaikan</td>
                    <td>{{ $item->tindakan_perbaikan ?? 'Belum ada tindakan tercatat.' }}</td>
                </tr>

                <tr>
                    <td>Hasil perbaikan</td>
                    <td>{{ $item->hasil_perbaikan ?? 'Belum ada hasil pemeriksaan.' }}</td>
                </tr>

                <tr>
                    <td>Tanggal selesai</td>
                    <td>
                        {{ $item->tanggal_perbaikan
                            ? \Carbon\Carbon::parse($item->tanggal_perbaikan)->format('d F Y')
                            : '-' }}
                    </td>
                </tr>

                <tr>
                    <td>Kondisi inventaris saat ini</td>
                    <td>
                        {{ ucfirst(str_replace('_', ' ', $item->barang?->kondisi ?? '-')) }}
                    </td>
                </tr>
            </table>

            <div class="photo-heading">
                DOKUMENTASI SEBELUM DAN SESUDAH PERBAIKAN
            </div>

            <table class="photo-grid">
                <tr>
                    <td>
                        <div class="photo-caption">SEBELUM PERBAIKAN</div>

                        @if ($fotoAwal && is_file($fotoAwal))
                            <img src="{{ $fotoAwal }}">
                        @else
                            <div class="no-photo">
                                Foto awal tidak tersedia.
                            </div>
                        @endif
                    </td>

                    <td>
                        <div class="photo-caption">SESUDAH PERBAIKAN</div>

                        @if ($fotoAkhir && is_file($fotoAkhir))
                            <img src="{{ $fotoAkhir }}">
                        @else
                            <div class="no-photo">
                                {{ $item->status_penanganan === 'selesai'
                                    ? 'Foto setelah perbaikan belum tersedia.'
                                    : 'Belum selesai diperbaiki.' }}
                            </div>
                        @endif
                    </td>
                </tr>
            </table>

        </div>

    @endforeach

@endif

<table class="signature">
    <tr>
        <td>
            Mengetahui,<br>
            Superadmin / Pihak Cabang

            <div class="signature-space"></div>

            <strong>(................................)</strong>
        </td>

        <td>
            Dibuat oleh,<br>
            Admin Tata Usaha

            <div class="signature-space"></div>

            <strong>{{ $namaAdmin }}</strong>
        </td>
    </tr>
</table>

<div class="footer">
    Dokumen resmi SISKO · Laporan Penanganan Inventaris
</div>

</body>
</html>
