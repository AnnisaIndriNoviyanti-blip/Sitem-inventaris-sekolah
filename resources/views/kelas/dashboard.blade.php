
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Beranda Kelas | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kelas/dashboard.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>

<body>

<div class="kelas-layout">

    <!-- SIDEBAR -->
    <aside class="kelas-sidebar">

        <a href="{{ url('/kelas') }}" class="kelas-brand">
            <div class="kelas-brand-icon">S</div>

            <div class="kelas-brand-text">
                <strong>SISKO</strong>
                <span>SISTEM PEMINJAMAN BARANG</span>
            </div>
        </a>

        <div class="kelas-sidebar-label">MENU UTAMA</div>
        
        <nav class="kelas-nav">

            
<a href="{{ auth()->user()->jenis_akun === 'organisasi'
            ? route('organisasi.dashboard')
            : route('kelas.dashboard') }}"
   class="kelas-nav-link">
    <span class="kelas-nav-icon">▦</span>
    <span>Beranda</span>
</a>


        <a href="{{ route('peminjaman.katalog') }}"
   class="kelas-nav-link">

    <span class="kelas-nav-icon">▤</span>

    <span>Katalog Barang TU</span>

</a>

        <a href="{{ route('peminjaman.index') }}"
            class="kelas-nav-link">
            <span class="kelas-nav-icon">▧</span>
            <span>Peminjaman Saya</span>

        @if (($peminjamanDiproses ?? 0) > 0)
            <span class="kelas-nav-badge">
                {{ $peminjamanDiproses }}
            </span>
        @endif
        </a>

        <a href="{{ route('laporan-kerusakan.index') }}"
            class="kelas-nav-link">
            <span class="kelas-nav-icon">♧</span>
            <span>Laporan Kerusakan</span>
        </a>

        </nav>


        <!-- INFORMASI AKUN -->
        <div class="kelas-sidebar-bottom">

            <div class="kelas-account-card">
                <span class="kelas-account-label">AKUN AKTIF</span>

                <div class="kelas-account-user">
                    <div class="kelas-account-avatar">
                        {{ strtoupper(substr(auth()->user()->nama ?? 'K', 0, 1)) }}
                    </div>

                    <div class="kelas-account-info">
                        <strong>
                            {{ auth()->user()->nama ?? 'Pengguna Kelas' }}
                        </strong>
                        <span>Pengguna SISKO</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit" class="kelas-logout">
    <span class="kelas-logout-icon">⇥</span>
    <span class="kelas-logout-text">Keluar dari akun</span>
</button>
            </form>

        </div>

    </aside>

    <!-- AREA UTAMA -->
    <main class="kelas-main">

        <!-- NAVBAR -->
        <header class="kelas-topbar">

            <label class="kelas-search">
                <span>⌕</span>

                <input
                    type="search"
                    id="kelasSearch"
                    placeholder="Cari barang atau peminjaman..."
                >
            </label>

            <div class="kelas-topbar-right">

                <div class="kelas-profile">

                    <div class="kelas-profile-avatar">
                        {{ strtoupper(substr(auth()->user()->nama ?? 'S', 0, 1)) }}
                    </div>

                    <div class="kelas-profile-info">
                        <strong>{{ auth()->user()->nama }}</strong>
                        <span>{{ ucfirst(auth()->user()->role) }}</span>
                    </div>

                </div>

            </div>

        </header>

        <!-- KONTEN DASHBOARD -->
        <div class="kelas-content">

            <!-- JUDUL -->
            <section class="kelas-heading">

                <div>
                    <span class="kelas-eyebrow">AREA PENGGUNA</span>

                    <h1>Beranda Kelas</h1>

                    <p>
                        Temukan barang yang kamu butuhkan, ajukan
                        peminjaman, dan pantau status permintaanmu
                        melalui satu dashboard.
                    </p>
                </div>

                <a href="{{ url('/peminjam/katalog') }}"
                   class="kelas-primary-button">
                    <span>＋</span>
                    Cari barang
                </a>

            </section>

            <!-- KARTU STATISTIK -->
            <section class="kelas-stats-grid">

                <article class="kelas-stat-card">
                    <div class="kelas-stat-top">
                        <span class="kelas-stat-label">
                            TOTAL PEMINJAMAN
                        </span>

                        <span class="kelas-stat-icon green">
                            ▤
                        </span>
                    </div>

                    <h2>{{ $totalPeminjaman ?? 0 }}</h2>

                    <p>
                        Seluruh pengajuan peminjaman akunmu
                    </p>
                </article>

                <article class="kelas-stat-card">
                    <div class="kelas-stat-top">
                        <span class="kelas-stat-label">
                            SEDANG DIPINJAM
                        </span>

                        <span class="kelas-stat-icon yellow">
                            ▧
                        </span>
                    </div>

                    <h2>{{ $sedangDipinjam ?? 0 }}</h2>

                    <p>
                        Peminjaman yang masih berlangsung
                    </p>
                </article>

                <article class="kelas-stat-card">
                    <div class="kelas-stat-top">
                        <span class="kelas-stat-label">
                            MENUNGGU PERSETUJUAN
                        </span>

                        <span class="kelas-stat-icon neutral">
                            ◷
                        </span>
                    </div>

                    <h2>{{ $peminjamanDiproses ?? 0 }}</h2>

                    <p>
                        Pengajuan yang menunggu pemeriksaan TU
                    </p>
                </article>

                <article class="kelas-stat-card">
                    <div class="kelas-stat-top">
                        <span class="kelas-stat-label">
                            LAPORAN KERUSAKAN
                        </span>

                        <span class="kelas-stat-icon orange">
                            ♧
                        </span>
                    </div>

                    <h2>{{ $totalLaporanKerusakan ?? 0 }}</h2>

                    <p>
                        Laporan kerusakan dari akunmu
                    </p>
                </article>

            </section>

            <!-- KATALOG DAN STATUS -->
            <section class="kelas-main-grid">

                <!-- BARANG POPULER -->
                <article class="kelas-panel">

                    <div class="kelas-panel-header">
                        <div>
                            <h2>Barang yang sering dipinjam</h2>

                            <p>
                                Lihat beberapa barang yang tersedia
                                untuk kebutuhan sekolah.
                            </p>
                        </div>

                        <a href="{{ url('/kelas/katalog') }}"
                           class="kelas-text-link">
                            Lihat katalog →
                        </a>
                    </div>

                    <div class="kelas-catalog-list">

                        @forelse (($barangPopuler ?? collect()) as $barang)
                        
                        <div class="kelas-catalog-item">
                            <div class="kelas-item-icon">▤</div>
                            <div class="kelas-item-info">
                            <strong>
                            {{ $barang->barang->nama_barang ?? 'Barang TU' }}
                        </strong>
                    <span>
                        Total diajukan: {{ $barang->total_dipinjam }} unit
                    </span>
                    </div>
                    <div class="kelas-item-stock">
                        <span class="kelas-stock-dot"></span>
                        @if (
                            $barang->barang &&
                            $barang->barang->status === 'tersedia' &&
                            $barang->barang->kondisi === 'baik'
                            )
                            Tersedia
                            @else
                            Tidak tersedia
                            @endif
                    </div>
                </div>

                        @empty

                            <div class="kelas-empty-state">
                                <div class="kelas-empty-icon">▤</div>

                                <strong>Katalog barang</strong>

                                <p>
                                    Daftar barang akan tampil di sini
                                    setelah data katalog tersedia.
                                </p>

                                <a href="{{ url('/kelas/katalog') }}">
                                    Buka katalog barang →
                                </a>
                            </div>

                        @endforelse

                    </div>

                </article>

                <!-- STATUS PEMINJAMAN -->
                <article class="kelas-panel">

                    <div class="kelas-panel-header">
                        <div>
                            <h2>Status peminjaman saya</h2>

                            <p>
                                Pantau perkembangan pengajuan terakhirmu.
                            </p>
                        </div>

                        <span class="kelas-count-badge">
                            {{ count($peminjamanTerbaru ?? []) }} PENGAJUAN
                        </span>
                    </div>

                    <div class="kelas-request-list">

                        @forelse (($peminjamanTerbaru ?? collect()) as $pinjam)

                            <div class="kelas-request-item">

                                <div class="kelas-request-icon">
                                    ▧
                                </div>

                                <div class="kelas-request-info">
                                    
                                <strong>
                                {{ $pinjam->detail
                                ->map(fn ($detail) => $detail->barang->nama_barang ?? null)
                                ->filter()
                                ->join(', ') ?: 'Peminjaman barang' }}
                                </strong>

                                    <span>
                                        @if (!empty($pinjam->created_at))
                                            {{ $pinjam->created_at->format('d M Y') }}
                                        @else
                                            Pengajuan peminjaman
                                        @endif
                                    </span>

                                    <small class="kelas-status
                                        {{ strtolower($pinjam->status ?? 'menunggu') === 'disetujui'
                                            ? 'status-approved'
                                            : (strtolower($pinjam->status ?? '') === 'ditolak'
                                                ? 'status-rejected'
                                                : 'status-pending') }}">

                                        {{ ucfirst($pinjam->status ?? 'Menunggu') }}

                                    </small>

                                </div>

                            </div>

                        @empty

                            <div class="kelas-empty-state">
                                <div class="kelas-empty-icon">◷</div>

                                <strong>Belum ada peminjaman</strong>

                                <p>
                                    Pengajuan yang kamu buat akan
                                    ditampilkan di bagian ini.
                                </p>
                            </div>

                        @endforelse

                    </div>

                    <div class="kelas-panel-footer">
                        <a href="{{ url('/kelas/peminjaman') }}"
                           class="kelas-outline-button">
                            Lihat semua peminjaman
                        </a>
                    </div>

                </article>

            </section>

            <!-- BAGIAN BAWAH -->
            <section class="kelas-bottom-grid">

                <!-- PANDUAN -->
                <article class="kelas-panel">

                    <div class="kelas-panel-header">
                        <div>
                            <h2>Alur peminjaman barang</h2>

                            <p>
                                Ikuti langkah berikut saat meminjam barang.
                            </p>
                        </div>
                    </div>

                    <div class="kelas-guide-list">

                        <div class="kelas-guide-item">
                            <span class="kelas-guide-number">01</span>

                            <div>
                                <strong>Pilih barang</strong>
                                <p>
                                    Cari barang yang dibutuhkan
                                    melalui katalog.
                                </p>
                            </div>
                        </div>

                        <div class="kelas-guide-item">
                            <span class="kelas-guide-number">02</span>

                            <div>
                                <strong>Ajukan peminjaman</strong>
                                <p>
                                    Isi formulir dan tanggal
                                    peminjaman dengan benar.
                                </p>
                            </div>
                        </div>

                        <div class="kelas-guide-item">
                            <span class="kelas-guide-number">03</span>

                            <div>
                                <strong>Tunggu verifikasi TU</strong>
                                <p>
                                    Periksa status pengajuanmu
                                    melalui menu Peminjaman Saya.
                                </p>
                            </div>
                        </div>

                    </div>

                </article>

                <!-- LAPORAN KERUSAKAN -->
                <article class="kelas-panel kelas-damage-panel">

                    <div class="kelas-panel-header">
                        <div>
                            <h2>Barang mengalami kerusakan?</h2>

                            <p>
                                Laporkan kondisi barang agar dapat
                                segera ditindaklanjuti oleh Admin TU.
                            </p>
                        </div>
                    </div>

                    <div class="kelas-damage-content">

                        <div class="kelas-damage-icon">
                            ♧
                        </div>

                        <div>
                            <strong>Bantu menjaga inventaris sekolah</strong>

                            <p>
                                Sampaikan laporan jika barang yang
                                dipinjam rusak atau mengalami kendala.
                            </p>
                        </div>

                        <a href="{{ url('/laporan-kerusakan') }}"
                           class="kelas-primary-button">
                            Buat laporan <span>→</span>
                        </a>

                    </div>

                </article>

            </section>

            <footer class="kelas-footer">
                <span>SISKO · SMKN 1 KOTA BEKASI</span>
                <span>Sistem Peminjaman Barang Tata Usaha</span>
            </footer>

        </div>

    </main>

</div>

<script>
    const kelasSearch = document.getElementById('kelasSearch');

    kelasSearch?.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();

        document.querySelectorAll(
            '.kelas-catalog-item, .kelas-request-item'
        ).forEach(function (item) {
            item.style.display = item.textContent
                .toLowerCase()
                .includes(keyword) ? '' : 'none';
        });
    });
</script>

</body>
</html>
