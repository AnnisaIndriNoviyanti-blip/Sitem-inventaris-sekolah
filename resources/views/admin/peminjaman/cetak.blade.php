
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buku Peminjaman Barang TU</title>
    <link rel="stylesheet" href="{{ asset('css/admin/cetak-peminjaman.css') }}">
</head>
<body>
    <div class="print-toolbar">
        <a href="{{ route('admin.peminjaman.index') }}">← Kembali</a>
        <button type="button" onclick="window.print()">Cetak Buku</button>
    </div>

    <main class="paper">
        <h1>BUKU PEMINJAMAN BARANG TATA USAHA</h1>
        <p class="subtitle">SISKO — Sistem Peminjaman Barang Sekolah</p>

        <table>
            <thead>
                <tr>
                    <th rowspan="2">NAMA</th>
                    <th rowspan="2">NO</th>
                    <th colspan="4">BARANG YANG DIPINJAM</th>
                    <th colspan="2">TANGGAL</th>
                    <th rowspan="2">TANDA TANGAN</th>
                </tr>
                <tr>
                    <th>LCD</th>
                    <th>KABEL</th>
                    <th>SPEAKER</th>
                    <th>LAPTOP</th>
                    <th>PEMINJAMAN</th>
                    <th>PENGEMBALIAN</th>
                </tr>
            </thead>

            <tbody>
            @forelse ($peminjaman as $item)
                @php
                    $details = $item->detail->filter(
                        fn ($detail) => $detail->barang !== null
                    );

                    $kategori = function ($barang) {
                        if ($barang->jenis_barang) {
                            return $barang->jenis_barang;
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
                    };

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

                    $kode = $paket->pluck('barang.kode_barang')
                        ->merge($speaker->pluck('barang.kode_barang'))
                        ->merge($laptop->pluck('barang.kode_barang'))
                        ->implode(', ');

                    $tanggalKembali = $item->tanggal_kembali
                        ?? $item->ttd_pengembalian_at;
                @endphp

                <tr>
                    <td>
                        {{ $item->nama_peminjam
                            ?? $item->user?->nama
                            ?? '-' }}
                    </td>

                    <td class="kode">{{ $kode ?: '-' }}</td>

                    <td class="check">
                        {{ $paket->isNotEmpty() ? '✓' : '' }}
                    </td>

                    <td class="check">
                        {{ $paket->isNotEmpty() ? '✓' : '' }}
                    </td>

                    <td class="check">
                        {{ $speaker->isNotEmpty() ? '✓' : '' }}
                    </td>

                    <td class="check">
                        {{ $laptop->isNotEmpty() ? '✓' : '' }}
                    </td>

                    <td>
                        {{ $item->tanggal_pinjam?->format('d/m/Y') ?? '-' }}
                    </td>

                    <td>
                        {{ $tanggalKembali
                            ? \Carbon\Carbon::parse($tanggalKembali)->format('d/m/Y')
                            : '' }}
                    </td>

                    <td class="signature">
                        @if ($item->ttd_peminjam)
                            <img
                                src="{{ asset('storage/' . $item->ttd_peminjam) }}"
                                alt="Tanda tangan peminjam">
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty">
                        Belum ada peminjaman yang disetujui.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </main>
</body>
</html>
