
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kerusakan Saya | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/laporan-kerusakan.css') }}">
    <link rel="stylesheet" href="{{ asset('css/peminjaman.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kelas/dashboard.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="app-layout">

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
            class="kelas-nav-link active">
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

    <main class="main-area">

        <header class="topbar">
            <label class="search-box">
                <span>⌕</span>
                <input type="search"
                       id="topSearch"
                       placeholder="Cari laporan kerusakan">
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

        <div class="damage-content">

            <section class="damage-heading">
                <div>
                    <span class="damage-eyebrow">
                        RIWAYAT PELAPORAN
                    </span>

                    <h1>Laporan Kerusakan Saya</h1>

                    <p>
                        Pantau laporan kerusakan barang yang sudah kamu
                        kirim ke Tata Usaha, termasuk status dan
                        tanggapan admin.
                    </p>
                </div>

                <a href="{{ url('/peminjaman') }}" class="damage-btn">
                    Lihat peminjaman →
                </a>
            </section>

            @if (session('success'))
                <div class="damage-alert success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="damage-alert error">
                    {{ session('error') }}
                </div>
            @endif

            @php
                $jumlahMenunggu = \App\Models\LaporanKerusakan::whereHas(
                    'peminjaman',
                    fn ($q) => $q->where('user_id', auth()->id())
                )->where('status', 'menunggu')->count();

                $jumlahDiproses = \App\Models\LaporanKerusakan::whereHas(
                    'peminjaman',
                    fn ($q) => $q->where('user_id', auth()->id())
                )->where('status', 'diproses')->count();

                $jumlahSelesai = \App\Models\LaporanKerusakan::whereHas(
                    'peminjaman',
                    fn ($q) => $q->where('user_id', auth()->id())
                )->where('status', 'selesai')->count();
            @endphp

            <section class="damage-stats">

                <article class="damage-stat">
                    <div class="damage-stat-top">
                        <span class="damage-stat-label">Menunggu</span>
                        <span class="damage-stat-icon amber">◷</span>
                    </div>
                    <h2>{{ $jumlahMenunggu }}</h2>
                    <p>Laporan menunggu pemeriksaan</p>
                </article>

                <article class="damage-stat">
                    <div class="damage-stat-top">
                        <span class="damage-stat-label">Sedang diproses</span>
                        <span class="damage-stat-icon">⚒</span>
                    </div>
                    <h2>{{ $jumlahDiproses }}</h2>
                    <p>Laporan sedang ditindaklanjuti TU</p>
                </article>

                <article class="damage-stat">
                    <div class="damage-stat-top">
                        <span class="damage-stat-label">Selesai ditangani</span>
                        <span class="damage-stat-icon">✓</span>
                    </div>
                    <h2>{{ $jumlahSelesai }}</h2>
                    <p>Laporan yang sudah ditangani</p>
                </article>

                <article class="damage-stat">
                    <div class="damage-stat-top">
                        <span class="damage-stat-label">Total laporan</span>
                        <span class="damage-stat-icon blue">♧</span>
                    </div>
                    <h2>{{ $laporan->total() }}</h2>
                    <p>Seluruh laporan yang kamu kirim</p>
                </article>

            </section>

            <form class="damage-filter"
                  method="GET"
                  action="{{ route('laporan-kerusakan.index') }}">

                <input type="search"
                       name="cari"
                       value="{{ request('cari') }}"
                       placeholder="Cari barang atau keterangan">

                <select name="status" aria-label="Filter status">
                    <option value="">Semua status</option>

                    <option value="menunggu"
                        @selected(request('status') === 'menunggu')>
                        Menunggu
                    </option>

                    <option value="diproses"
                        @selected(request('status') === 'diproses')>
                        Diproses
                    </option>

                    <option value="selesai"
                        @selected(request('status') === 'selesai')>
                        Selesai
                    </option>

                    <option value="ditolak"
                        @selected(request('status') === 'ditolak')>
                        Ditolak
                    </option>
                </select>

                <div class="damage-filter-actions">
                    <button class="damage-btn" type="submit">
                        Cari laporan
                    </button>

                    <a class="damage-btn secondary"
                       href="{{ route('laporan-kerusakan.index') }}">
                        Reset
                    </a>
                </div>
            </form>

            <section class="damage-list-panel">

                <div class="damage-list-head">
                    <h2>Riwayat laporan</h2>
                    <p>
                        Informasi laporan dan tanggapan Tata Usaha
                        ditampilkan di bawah ini.
                    </p>
                </div>

                <div class="damage-cards" id="damageCards">

                    @forelse ($laporan as $item)

                        @php
                            $statusClass = [
                                'menunggu' => 'status-menunggu',
                                'diproses' => 'status-diproses',
                                'selesai' => 'status-selesai',
                                'ditolak' => 'status-ditolak',
                            ][$item->status] ?? 'status-menunggu';

                            $kodeLaporan = 'LK-' .
                                \Carbon\Carbon::parse($item->tanggal_laporan)
                                    ->format('ymd') .
                                '-' .
                                str_pad((string) $item->id, 2, '0', STR_PAD_LEFT);
                        @endphp

                        <article class="damage-card">

                            <div class="damage-card-top">
                                <div>
                                    <div class="damage-code">
                                        {{ $item->kode_laporan ?? $kodeLaporan }}
                                    </div>

                                    <h3>
                                        {{ $item->peminjamanDetail?->barang?->nama_barang
                                            ?? 'Barang tidak ditemukan' }}
                                    </h3>

                                    <p class="damage-description">
                                        {{ $item->keterangan ?: 'Tidak ada keterangan.' }}
                                    </p>
                                </div>

                                <div class="damage-badges">
                                    <span class="damage-badge {{ $statusClass }}">
                                        @if ($item->status === 'menunggu')
                                            ◷
                                        @elseif ($item->status === 'diproses')
                                            ⚒
                                        @elseif ($item->status === 'selesai')
                                            ✓
                                        @else
                                            !
                                        @endif

                                        {{ strtoupper($item->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="damage-meta">
                                <span>
                                    ♧ Peminjaman #{{ $item->peminjaman_id }}
                                </span>

                                <span>
                                    ▣
                                    {{ $item->jenis_laporan === 'saat_diterima'
                                        ? 'Kerusakan saat diterima'
                                        : 'Kerusakan selama dipinjam' }}
                                </span>

                                <span>
                                    ◷
                                    {{ \Carbon\Carbon::parse($item->tanggal_laporan)
                                        ->format('d M Y') }}
                                </span>

                                @if ($item->nomor_whatsapp)
                                    <span>
                                        WhatsApp: {{ $item->nomor_whatsapp }}
                                    </span>
                                @endif
                            </div>

                            @if ($item->bukti_laporan)
                                <div style="margin-top:13px;">
                                    <a class="damage-btn secondary small"
                                       href="{{ asset('storage/' . $item->bukti_laporan) }}"
                                       target="_blank"
                                       rel="noopener">
                                        Lihat bukti foto ↗
                                    </a>
                                </div>
                            @endif

                            <div class="damage-response
                                {{ $item->catatan_admin ? '' : 'pending-response' }}">

                                <strong>TANGGAPAN TATA USAHA</strong>

                                <p>
                                    {{ $item->catatan_admin
                                        ?: 'Belum ada tanggapan. Silakan cek kembali setelah laporan diperiksa.' }}
                                </p>
                            </div>

                        </article>

                    @empty

                        <div class="damage-empty">
                            <strong>Belum ada laporan kerusakan</strong>

                            Laporan yang kamu kirim akan muncul di sini.
                            Untuk melaporkan kerusakan, buka detail
                            peminjaman yang masih aktif.

                            <div style="margin-top:16px;">
                                <a href="{{ route('peminjaman.index') }}" class="damage-btn">
    Buka Peminjaman →
</a>
                            </div>
                        </div>

                    @endforelse

                </div>

                <div class="damage-pagination">
                    {{ $laporan->withQueryString()->links() }}
                </div>

            </section>

            <footer class="dashboard-footer">
                <span>SISKO · Sistem Peminjaman Barang Tata Usaha</span>
                <span>Riwayat Laporan</span>
            </footer>

        </div>
    </main>
</div>

<script>
    document.getElementById('topSearch')?.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();

        document.querySelectorAll('.damage-card').forEach(function (card) {
            card.style.display = card.textContent
                .toLowerCase()
                .includes(keyword) ? '' : 'none';
        });
    });
</script>

</body>
</html>
