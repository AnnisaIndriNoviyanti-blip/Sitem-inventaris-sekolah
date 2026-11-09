
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
        margin-bottom: 22px;
    }

    .header h1 {
        color: #285d46;
        font-size: 19px;
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

    .document-info {
        width: 100%;
        margin-bottom: 18px;
    }

    .document-info td {
        padding: 4px;
    }

    .section-title {
        color: #285d46;
        font-size: 12px;
        font-weight: bold;
        margin-bottom: 9px;
    }

    .damage-item {
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
        margin-bottom: 8px;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 12px;
    }

    .data-table td {
        border: 1px solid #dce5df;
        padding: 7px;
        vertical-align: top;
    }

    .data-table td:first-child {
        width: 32%;
        background: #f0f5f1;
        font-weight: bold;
    }

    .photo-title {
        font-weight: bold;
        color: #285d46;
        margin: 12px 0 8px;
    }

    .photo {
        text-align: center;
    }

    .photo img {
        max-width: 100%;
        max-height: 370px;
    }

    .no-photo {
        padding: 25px;
        background: #f3f4f3;
        text-align: center;
        color: #777;
    }

    .signature {
        width: 100%;
        margin-top: 45px;
        text-align: center;
    }

    .signature td {
        width: 50%;
        vertical-align: top;
        padding: 5px 20px;
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
    <h1>LAPORAN KERUSAKAN BARANG</h1>
    <h2>{{ $namaSekolah }}</h2>
    <p>SISKO — Sistem Peminjaman Barang Tata Usaha</p>
</div>

<table class="document-info">
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

<div class="section-title">
    DOKUMENTASI INVENTARIS YANG MENGALAMI KERUSAKAN
</div>

@if ($laporan->isEmpty())

    <p>Belum terdapat data inventaris rusak.</p>

@else

    @foreach ($laporan as $index => $item)

        @php
            $foto = $item->foto_bukti
                ? storage_path('app/public/' . $item->foto_bukti)
                : null;
        @endphp

        <div class="damage-item">

            <div class="item-title">
                {{ $index + 1 }}. {{ $item->barang?->nama_barang ?? 'Barang tidak ditemukan' }}
            </div>

            <table class="data-table">
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
                    <td>{{ ucfirst(str_replace('_', ' ', $item->status_penanganan ?? '-')) }}</td>
                </tr>

                <tr>
                    <td>Catatan kerusakan</td>
                    <td>{{ $item->catatan ?? '-' }}</td>
                </tr>

                <tr>
                    <td>Dicatat oleh</td>
                    <td>{{ $item->admin?->nama ?? $namaAdmin }}</td>
                </tr>
            </table>

            <div class="photo-title">
                BUKTI FOTO KERUSAKAN
            </div>

            <div class="photo">

                @if ($foto && is_file($foto))

                    <img src="{{ $foto }}">

                @else

                    <div class="no-photo">
                        Foto bukti tidak ditemukan.
                    </div>

                @endif

            </div>

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
            Dilaporkan oleh,<br>
            Admin Tata Usaha

            <div class="signature-space"></div>

            <strong>{{ $namaAdmin }}</strong>
        </td>
    </tr>
</table>

<div class="footer">
    Dokumen resmi SISKO · Laporan Kerusakan Inventaris
</div>

</body>
</html>
