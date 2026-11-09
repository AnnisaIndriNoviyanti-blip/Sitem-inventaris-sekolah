
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">

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
            <a href="{{ route('admin.dashboard') }}" class="nav-link active">
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

            <a href="{{ route('admin.kerusakan.index') }}" class="nav-link">
    <span class="nav-icon">♧</span>
    <span>Laporan Kerusakan</span>
    <span class="nav-badge warning">
        {{ $jumlahKerusakan ?? 0 }}
    </span>
</a>

            <a href="{{ url('/admin/riwayat') }}" class="nav-link">
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

    <!-- AREA UTAMA -->
    <main class="main-area">

        <!-- TOPBAR -->
        <header class="topbar">
            <label class="search-box">
                <span>⌕</span>
                <input type="search"
                       id="dashboardSearch"
                       placeholder="Cari barang, kode, atau peminjam">
            </label>

            <div class="topbar-right">
                

                <div class="profile">
                    <div class="profile-avatar">
                        {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                    </div>
                    <div class="profile-info">
                        <strong>{{ auth()->user()->nama }}</strong>
                        <span>Admin Tata Usaha</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- KONTEN DASHBOARD -->
        <div class="dashboard-content">

            <section class="page-heading">
                <div>
                    <span class="eyebrow">TATA USAHA</span>
                    <h1>Beranda Tata Usaha</h1>
                    <p>
                        Pantau pengelolaan barang, permintaan peminjaman
                        guru dan siswa, serta laporan kerusakan dalam
                        satu tampilan.
                    </p>
                </div>

                <a href="{{ route('barang-tu.index') }}" class="primary-button">
                    <span>▤</span> Barang Tata Usaha
                </a>
            </section>

            <!-- KARTU STATISTIK -->
            <section class="stats-grid">

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">BARANG TU</span>
                        <span class="stat-icon green-icon">▤</span>
                    </div>
                    <h2>{{ $totalBarang ?? '—' }}</h2>
                    <p>
                        {{ $totalUnitBarang ?? '—' }} unit tercatat dalam
                        pengelolaan Tata Usaha
                    </p>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">SEDANG DIPINJAM</span>
                        <span class="stat-icon yellow-icon">▧</span>
                    </div>
                    <h2>{{ $sedangDipinjam ?? '—' }}</h2>
                    <p>Barang yang sedang berada di luar ruang TU</p>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">MENUNGGU VERIFIKASI</span>
                        <span class="stat-icon neutral-icon">▤</span>
                    </div>
                    <h2>{{ $menungguVerifikasi ?? '—' }}</h2>
                    <p>Permintaan guru dan siswa yang perlu diperiksa</p>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">LAPORAN KERUSAKAN</span>
                        <span class="stat-icon orange-icon">♧</span>
                    </div>
                    <h2>{{ $jumlahKerusakan ?? '—' }}</h2>
                    <p>Laporan kerusakan barang yang perlu ditindaklanjuti</p>
                </article>

            </section>

            <!-- GRAFIK DAN TINDAKAN -->
            <section class="dashboard-columns">

                <article class="panel chart-panel">
                    <div class="panel-header">
                        <div>
                            <h2>Barang paling sering dipinjam</h2>
                            <p>Akumulasi jumlah unit yang pernah diajukan melalui sistem peminjaman.</p>
                        </div>
                    </div>

                    <div class="chart-body">
                        @php
                            $chartBarang = $barangTerpopuler ?? [
                                ['nama' => 'Proyektor', 'jumlah' => 4],
                                ['nama' => 'Tenda Kegiatan', 'jumlah' => 3],
                                ['nama' => 'Kabel HDMI', 'jumlah' => 3],
                                ['nama' => 'Bola Voli', 'jumlah' => 2],
                                ['nama' => 'Sound System', 'jumlah' => 2],
                            ];
                            $maksGrafik = max(
                                1,
                                collect($chartBarang)->max('jumlah') ?? 1
                            );
                        @endphp

                        <div class="bar-chart">
                            @foreach ($chartBarang as $barang)
                                <div class="chart-row">
                                    <div class="chart-name">
                                        {{ $barang['nama'] }}
                                    </div>
                                    <div class="bar-track">
                                        <div class="bar-fill"
                                             style="width: {{ min(100, ($barang['jumlah'] / $maksGrafik) * 100) }}%">
                                        </div>
                                    </div>
                                    <span class="bar-value">
                                        {{ $barang['jumlah'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="chart-axis">
                            <span></span>
                            <div class="axis-numbers">
                                @for ($i = 0; $i <= $maksGrafik; $i++)
                                    <span>{{ $i }}</span>
                                @endfor
                            </div>
                            <span></span>
                        </div>
                    </div>
                </article>

                <article class="panel action-panel">
                    <div class="panel-header action-heading">
                        <div>
                            <h2>Perlu tindakan Anda</h2>
                            <p>Permintaan peminjaman yang menunggu pemeriksaan Tata Usaha.</p>
                        </div>
                        <span class="request-badge">
                            {{ $menungguVerifikasi ?? 0 }} PERMINTAAN
                        </span>
                    </div>

                    <div class="request-list">
                        @forelse ($permintaanMenunggu as $permintaan)
                            <a href="{{ url('/admin/peminjaman') }}"
                               class="request-item">
                                <span class="request-icon">▤</span>
                                <span class="request-info">
                                    <strong>
                                        {{ $permintaan->nama_peminjam
                                            ?? $permintaan->nama
                                            ?? 'Peminjam' }}
                                    </strong>
                                    <small>
                                        {{ $permintaan->jumlah_barang ?? 1 }}
                                        jenis barang
                                        @if (!empty($permintaan->created_at))
                                            · {{ $permintaan->created_at->format('d M Y') }}
                                        @endif
                                    </small>
                                </span>
                                <span class="request-action">
                                    Periksa <span>→</span>
                                </span>
                            </a>
                        @empty
                            <div class="empty-state">
                                <span>✓</span>
                                <strong>Tidak ada permintaan tertunda</strong>
                                <p>Permintaan baru akan muncul di sini.</p>
                            </div>
                        @endforelse
                    </div>
                </article>

            </section>

            <!-- INFORMASI TAMBAHAN -->
            <section class="bottom-grid">

                <article class="panel">
                    <div class="panel-header">
                        <div>
                            <h2>Ketersediaan perlu perhatian</h2>
                            <p>Ringkasan kondisi inventaris barang.</p>
                        </div>
                        <a href="{{ route('barang-tu.index') }}" class="text-link">
                            Lihat semua →
                        </a>
                    </div>

                    <div class="summary-list">
                        <div class="summary-item">
                            <span class="summary-dot available"></span>
                            <span>Barang tersedia</span>
                            <strong>{{ $barangTersedia ?? '—' }}</strong>
                        </div>
                        <div class="summary-item">
                            <span class="summary-dot borrowed"></span>
                            <span>Barang sedang dipinjam</span>
                            <strong>{{ $sedangDipinjam ?? '—' }}</strong>
                        </div>
                        <div class="summary-item">
                            <span class="summary-dot damaged"></span>
                            <span>Barang dilaporkan rusak</span>
                            <strong>{{ $jumlahKerusakan ?? '—' }}</strong>
                        </div>
                    </div>
                </article>

                <article class="panel">
                    <div class="panel-header">
                        <div>
                            <h2>Aktivitas terbaru</h2>
                            <p>Ringkasan aktivitas pengelolaan terakhir.</p>
                        </div>
                        <a href="{{ url('/admin/riwayat') }}" class="text-link">
                            Lihat riwayat →
                        </a>
                    </div>

                    <div class="activity-list">
                        @forelse (($aktivitasTerbaru ?? collect()) as $aktivitas)
                            <div class="activity-item">
                                <span class="activity-icon">↻</span>
                                <div>
                                    <strong>
                                        {{ $aktivitas->keterangan
                                            ?? $aktivitas->aktivitas
                                            ?? 'Aktivitas baru' }}
                                    </strong>
                                    <small>
                                        @if (!empty($aktivitas->created_at))
                                            {{ $aktivitas->created_at->format('d M Y, H:i') }}
                                        @else
                                            Aktivitas sistem
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="empty-activity">
                                Belum ada data aktivitas terbaru.
                            </div>
                        @endforelse
                    </div>
                </article>

            </section>

            <footer class="dashboard-footer">
                <span>SISKO · Sistem Peminjaman Barang Tata Usaha</span>
                <span>Dashboard Administrator</span>
            </footer>

        </div>
    </main>
</div>

<script>
    const dashboardSearch = document.getElementById('dashboardSearch');

    dashboardSearch?.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();

        document.querySelectorAll(
            '.request-item, .activity-item, .summary-item, .chart-row'
        ).forEach(function (item) {
            item.style.display = item.textContent
                .toLowerCase()
                .includes(keyword) ? '' : 'none';
        });
    });
</script>
</body>
</html>
