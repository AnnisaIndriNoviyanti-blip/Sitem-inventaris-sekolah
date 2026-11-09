
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Beranda Organisasi | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/organisasi/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/peminjaman.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kelas/dashboard.css') }}">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>

<body>

<div class="organisasi-layout">

    <!-- SIDEBAR -->
   <aside class="kelas-sidebar">

        <a href="{{ url('/organisasi') }}" class="kelas-brand">
            <div class="kelas-brand-icon">S</div>

            <div class="kelas-brand-text">
                <strong>SISKO</strong>
                <span>SISTEM PEMINJAMAN BARANG</span>
            </div>
        </a>

        <div class="kelas-sidebar-label">MENU UTAMA</div>
        
        <nav class="kelas-nav">

            <a href="{{ route('organisasi.dashboard') }}"
            class="kelas-nav-link active">
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
    <main class="organisasi-main">

        <!-- NAVBAR -->
        <header class="organisasi-topbar">

            <label class="organisasi-search">
                <span>⌕</span>

                <input
                    type="search"
                    id="kelasSearch"
                    placeholder="Cari barang atau peminjaman organisasi..."
                >
            </label>

            <div class="topbar-right">
                <div class="profile">
                    <div class="profile-avatar">
                        {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name, 0, 1)) }}
                    </div>

                    <div class="profile-info">
                        <strong>
                            {{ auth()->user()->nama ?? auth()->user()->name }}
                        </strong>
                        <span>{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                </div>
            </div>

        </header>

        <!-- KONTEN DASHBOARD -->
        <div class="organisasi-content">

            <!-- JUDUL -->
            <section class="organisasi-heading">

                <div>
                    <span class="organisasi-eyebrow">AREA ORGANISASI / EKSKUL</span>

                    <h1>Beranda Organisasi</h1>

                    <p>
                        Temukan barang yang dibutuhkan organisasi, ajukan
                        peminjaman, dan pantau status permintaanmu
                        melalui satu dashboard.
                    </p>
                </div>

                <a href="{{ route('peminjaman.katalog') }}"
                   class="organisasi-primary-button">
                    <span>＋</span>
                    Cari barang
                </a>

            </section>

            <!-- KARTU STATISTIK -->
            <section class="organisasi-stats-grid">

                <article class="organisasi-stat-card">
                    <div class="organisasi-stat-top">
                        <span class="organisasi-stat-label">
                            TOTAL PEMINJAMAN
                        </span>

                        <span class="organisasi-stat-icon green">
                            ▤
                        </span>
                    </div>

                    <h2>{{ $totalPeminjaman ?? 0 }}</h2>

                    <p>
                        Seluruh pengajuan peminjaman organisasi
                    </p>
                </article>

                <article class="organisasi-stat-card">
                    <div class="organisasi-stat-top">
                        <span class="organisasi-stat-label">
                            SEDANG DIPINJAM
                        </span>

                        <span class="organisasi-stat-icon yellow">
                            ▧
                        </span>
                    </div>

                    <h2>{{ $sedangDipinjam ?? 0 }}</h2>

                    <p>
                        Peminjaman aktif organisasi / ekskul
                    </p>
                </article>

                <article class="organisasi-stat-card">
                    <div class="organisasi-stat-top">
                        <span class="organisasi-stat-label">
                            MENUNGGU PERSETUJUAN
                        </span>

                        <span class="organisasi-stat-icon neutral">
                            ◷
                        </span>
                    </div>

                    <h2>{{ $peminjamanDiproses ?? 0 }}</h2>

                    <p>
                        Pengajuan organisasi yang menunggu pemeriksaan TU
                    </p>
                </article>

                <article class="organisasi-stat-card">
                    <div class="organisasi-stat-top">
                        <span class="organisasi-stat-label">
                            LAPORAN KERUSAKAN
                        </span>

                        <span class="organisasi-stat-icon orange">
                            ♧
                        </span>
                    </div>

                    <h2>{{ $totalLaporanKerusakan ?? 0 }}</h2>

                    <p>
                        Laporan kerusakan organisasi / ekskul
                    </p>
                </article>

            </section>

            <!-- KATALOG DAN STATUS -->
            <section class="organisasi-main-grid">

                <!-- BARANG POPULER -->
                <article class="organisasi-panel">

                    <div class="organisasi-panel-header">
                        <div>
                            <h2>Barang yang sering dipinjam</h2>

                            <p>
                                Lihat beberapa barang yang tersedia
                                untuk kegiatan organisasi dan ekstrakurikuler.
                            </p>
                        </div>

                        <a href="{{ route('peminjaman.create') }}"
                           class="organisasi-text-link">
                            Lihat katalog →
                        </a>
                    </div>

                    <div class="organisasi-catalog-list">

                        @forelse (($barangPopuler ?? collect()) as $barang)
                        
                        <div class="organisasi-catalog-item">
                            <div class="organisasi-item-icon">▤</div>
                            <div class="organisasi-item-info">
                            <strong>
                            {{ $barang->barang->nama_barang ?? 'Barang TU' }}
                        </strong>
                    <span>
                        Total diajukan: {{ $barang->total_dipinjam }} unit
                    </span>
                    </div>
                    <div class="organisasi-item-stock">
                        <span class="organisasi-stock-dot"></span>
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

                            <div class="organisasi-empty-state">
                                <div class="organisasi-empty-icon">▤</div>

                                <strong>Katalog barang</strong>

                                <p>
                                    Daftar barang akan tampil di sini
                                    setelah data katalog tersedia.
                                </p>

                                <a href="{{ route('peminjaman.katalog') }}">
                                    Buka katalog barang →
                                </a>
                            </div>

                        @endforelse

                    </div>

                </article>

                <!-- STATUS PEMINJAMAN -->
                <article class="organisasi-panel">

                    <div class="organisasi-request-list">

                        @forelse (($peminjamanTerbaru ?? collect()) as $pinjam)

                            <div class="organisasi-request-item">

                                <div class="organisasi-request-icon">
                                    ▧
                                </div>

                                <div class="organisasi-request-info">
                                    
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

                                    <small class="organisasi-status
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

                            <div class="organisasi-empty-state">
                                <div class="organisasi-empty-icon">◷</div>

                                <strong>Lakukan Peminjaman</strong>

                                <p>
                                    Pengajuan yang dibuat organisasi akan
                                    ditampilkan di bagian ini.
                                </p>
                            </div>

                        @endforelse

                    </div>

                    <div class="organisasi-panel-footer">
                        <a href="{{ route('peminjaman.index') }}"
                           class="organisasi-outline-button">
                            Lihat semua peminjaman
                        </a>
                    </div>

                </article>

            </section>

            <!-- BAGIAN BAWAH -->
            <section class="organisasi-bottom-grid">

                <!-- PANDUAN -->
                <article class="organisasi-panel">

                    <div class="organisasi-panel-header">
                        <div>
                            <h2>Alur peminjaman barang</h2>

                            <p>
                                Ikuti langkah berikut saat meminjam barang.
                            </p>
                        </div>
                    </div>

                    <div class="organisasi-guide-list">

                        <div class="organisasi-guide-item">
                            <span class="organisasi-guide-number">01</span>

                            <div>
                                <strong>Pilih barang</strong>
                                <p>
                                    Cari barang yang dibutuhkan
                                    melalui katalog.
                                </p>
                            </div>
                        </div>

                        <div class="organisasi-guide-item">
                            <span class="organisasi-guide-number">02</span>

                            <div>
                                <strong>Ajukan peminjaman</strong>
                                <p>
                                    Isi formulir dan tanggal
                                    peminjaman dengan benar.
                                </p>
                            </div>
                        </div>

                        <div class="organisasi-guide-item">
                            <span class="organisasi-guide-number">03</span>

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
                <article class="organisasi-panel organisasi-damage-panel">

                    <div class="organisasi-panel-header">
                        <div>
                            <h2>Barang mengalami kerusakan?</h2>

                            <p>
                                Laporkan kondisi barang agar dapat
                                segera ditindaklanjuti oleh Admin TU.
                            </p>
                        </div>
                    </div>

                    <div class="organisasi-damage-content">

                        <div class="organisasi-damage-icon">
                            ♧
                        </div>

                        <div>
                            <strong>Bantu menjaga inventaris sekolah</strong>

                            <p>
                                Sampaikan laporan jika barang yang
                                dipinjam rusak atau mengalami kendala.
                            </p>
                        </div>

                        <a href="{{ route('laporan-kerusakan.index') }}"
                           class="organisasi-primary-button">
                            Buat laporan <span>→</span>
                        </a>

                    </div>

                </article>

            </section>

            <footer class="organisasi-footer">
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
            '.organisasi-catalog-item, .organisasi-request-item'
        ).forEach(function (item) {
            item.style.display = item.textContent
                .toLowerCase()
                .includes(keyword) ? '' : 'none';
        });
    });
</script>

</body>
</html>
