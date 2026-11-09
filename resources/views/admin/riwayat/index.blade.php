<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/riwayat.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>

<body>
<div class="app-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <a href="{{ url('/admin') }}" class="brand">
            <div class="brand-icon">S</div>
            <div class="brand-text">
                <strong>SISKO</strong>
                <span>TATA USAHA SEKOLAH</span>
            </div>
        </a>

        <div class="sidebar-label">PENGELOLAAN</div>

        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                <span class="nav-icon">▦</span>
                <span>Beranda</span>
            </a>

            <a href="{{ route('barang-tu.index') }}" class="nav-link">
    <span class="nav-icon">▤</span>
    <span>Barang Tata Usaha</span>
</a>

            <a href="{{ route('admin.peminjaman.index') }}" class="nav-link">
    <span class="nav-icon">▧</span>
    <span>Verifikasi Peminjaman</span>
    <span class="nav-badge">{{ $menungguVerifikasi ?? 0 }}</span>
</a>

            <a href="{{ url('/admin/pengembalian') }}" class="nav-link">
                <span class="nav-icon">↻</span>
                <span>Pengembalian</span>
            </a>

            <a href="{{ url('/admin/kerusakan') }}" class="nav-link">
                <span class="nav-icon">♧</span>
                <span>Laporan Kerusakan</span>
                <span class="nav-badge warning">{{ $jumlahKerusakan ?? 0 }}</span>
            </a>

            <a href="{{ url('/admin/riwayat') }}" class="nav-link active">
                <span class="nav-icon">◷</span>
                <span>Riwayat & Rekap</span>
            </a>

            <a href="{{ route('admin.akun.index') }}" class="nav-link">
                <span class="nav-icon">♙</span>
                <span>Manajemen Akun</span>
            </a>
            <a href="{{ route('admin.pengamanan.index') }}" class="nav-link">
    <span class="nav-icon">♧</span>
    <span>Pengamanan Akun</span>
</a>
        </nav>

        <div class="sidebar-bottom">
            <div class="logged-in-card">
                <span class="logged-label">MASUK SEBAGAI</span>
                <strong>{{ auth()->user()->nama }}</strong>
                <span>Admin Tata Usaha</span>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="logout-link" type="submit">
    <span class="logout-icon">⇥</span>
    <span class="logout-text">Keluar</span>
</button>
            </form>
        </div>
        </aside>

    <main class="riwayat-page">

    {{-- Header halaman --}}
    <div class="page-heading riwayat-heading">
        <div>
            <span class="riwayat-eyebrow">ADMINISTRATOR</span>
            <h1>Riwayat & Rekap</h1>
            <p>Ringkasan transaksi peminjaman, pengembalian, dan laporan kerusakan barang.</p>
        </div>
    </div>

    {{-- Kartu statistik --}}
    <div class="stats-grid riwayat-stats">

        <div class="stat-card">
            <div class="stat-info">
                <span>Total Transaksi</span>
                <h3>{{ $totalTransaksi ?? 0 }}</h3>
                <small>Seluruh transaksi peminjaman</small>
            </div>
            <div class="stat-icon">↗</div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Dikembalikan</span>
                <h3>{{ $totalDikembalikan ?? 0 }}</h3>
                <small>Transaksi selesai</small>
            </div>
            <div class="stat-icon">✓</div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Ditolak</span>
                <h3>{{ $totalDitolak ?? 0 }}</h3>
                <small>Pengajuan tidak disetujui</small>
            </div>
            <div class="stat-icon">×</div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Menunggu Verifikasi</span>
                <h3>{{ $menungguVerifikasi ?? 0 }}</h3>
                <small>Pengajuan perlu diperiksa</small>
            </div>
            <div class="stat-icon">◷</div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Laporan Kerusakan</span>
                <h3>{{ $totalLaporan ?? 0 }}</h3>
                <small>Total laporan yang tercatat</small>
            </div>
            <div class="stat-icon">!</div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Kerusakan Aktif</span>
                <h3>{{ $jumlahKerusakanAktif ?? 0 }}</h3>
                <small>Laporan yang masih ditangani</small>
            </div>
            <div class="stat-icon">⚒</div>
        </div>

    </div>

    {{-- Rekap barang --}}
    <div class="panel riwayat-panel">
        <div class="panel-header riwayat-panel-header">
            <div>
                <h2>Rekap Barang</h2>
                <p>Ringkasan jumlah peminjaman dan laporan kerusakan per barang.</p>
            </div>
        </div>

        <div class="riwayat-table-wrapper">
            <table class="riwayat-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Barang</th>
                        <th>Total Dipinjam</th>
                        <th>Total Kerusakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekapBarang as $index => $barang)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>
                                    {{ $barang->nama_barang ?? 'Barang tidak ditemukan' }}
                                </strong>
                            </td>
                            <td>
                                <span class="riwayat-number">
                                    {{ $barang->total_unit ?? 0 }}
                                </span>
                            </td>
                            <td>
                                <span class="riwayat-number">
                                    {{ $barang->jumlah_laporan ?? 0 }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="riwayat-empty">
                                Belum ada data rekap barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Riwayat transaksi --}}
    <div class="panel riwayat-panel">
        <div class="panel-header riwayat-panel-header">
            <div>
                <h2>Riwayat Peminjaman</h2>
                <p>Daftar transaksi peminjaman berdasarkan data terbaru.</p>
            </div>

            <span class="riwayat-total">
                {{ isset($peminjaman) ? $peminjaman->total() : 0 }} transaksi
            </span>
        </div>

        <div class="riwayat-table-wrapper">
            <table class="riwayat-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Peminjam</th>
                        <th>Barang</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($peminjaman as $index => $transaksi)
                        <tr>
                            <td>
                                {{ $peminjaman->firstItem() + $index }}
                            </td>

                            <td>
    <strong>
        {{ $transaksi->nama_peminjam ?? $transaksi->user?->nama ?? '-' }}
    </strong>

    <small class="riwayat-subtext">
        ID transaksi: {{ $transaksi->id }}
    </small>
</td>

                            <td>
                                @forelse ($transaksi->detail as $detail)
                                    <div class="riwayat-item">
                                        {{ $detail->barang->nama_barang ?? 'Barang tidak ditemukan' }}
                                        @if (! $loop->last)
                                            <span>,</span>
                                        @endif
                                    </div>
                                @empty
                                    <span class="riwayat-muted">Tidak ada detail barang</span>
                                @endforelse
                            </td>

                            <td>
                                {{ $transaksi->tanggal_pinjam
                                    ? \Carbon\Carbon::parse($transaksi->tanggal_pinjam)->format('d/m/Y')
                                    : '-' }}
                            </td>

                            <td>
    {{
        ($transaksi->tanggal_kembali ?? $transaksi->ttd_pengembalian_at)
            ? \Carbon\Carbon::parse(
                $transaksi->tanggal_kembali ?? $transaksi->ttd_pengembalian_at
            )->format('d/m/Y')
            : '-'
    }}
</td>

                            <td>
                                @php
                                    $status = strtolower($transaksi->status ?? 'menunggu');

                                    $statusClass = match ($status) {
                                        'dikembalikan' => 'status-selesai',
                                        'disetujui', 'dipinjam' => 'status-berjalan',
                                        'ditolak' => 'status-ditolak',
                                        'menunggu' => 'status-menunggu',
                                        default => 'status-lain',
                                    };
                                @endphp

                                <span class="riwayat-status {{ $statusClass }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="riwayat-empty">
                                Belum ada riwayat peminjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Navigasi halaman --}}
        @if (method_exists($peminjaman, 'links'))
            <div class="riwayat-pagination">
                {{ $peminjaman->links() }}
            </div>
        @endif
    </div>

    </main>
</div>

</body>
</html>