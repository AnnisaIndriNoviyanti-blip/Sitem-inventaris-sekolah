<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kerusakan | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/kerusakan.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/inventaris-rusak.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">


</head>
<body>
<div class="app-layout">
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <div class="brand-icon">S</div>
            <div class="brand-text">
                <strong>SISKO</strong>
                <span>TATA USAHA SEKOLAH</span>
            </div>
        </a>

        <div class="sidebar-label">PENGELOLAAN</div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-link">
                <span class="nav-icon">▦</span><span>Beranda</span>
            </a>
            <a href="{{ route('barang-tu.index') }}" class="nav-link">
                <span class="nav-icon">▤</span><span>Barang Tata Usaha</span>
            </a>
            <a href="{{ route('admin.peminjaman.index') }}" class="nav-link">
                <span class="nav-icon">▧</span><span>Verifikasi Peminjaman</span>
                <span class="nav-badge">{{ $menungguVerifikasi ?? 0 }}</span>
            </a>
            <a href="{{ url('/admin/pengembalian') }}" class="nav-link">
                <span class="nav-icon">↻</span><span>Pengembalian</span>
            </a>
            <a href="{{ route('admin.kerusakan.index') }}" class="nav-link active">
                <span class="nav-icon">♧</span><span>Laporan Kerusakan</span>
                <span class="nav-badge warning">{{ $jumlahKerusakan ?? 0 }}</span>
            </a>
            <a href="{{ url('/admin/riwayat') }}" class="nav-link">
                <span class="nav-icon">◷</span><span>Riwayat & Rekap</span>
            </a>
            <a href="{{ route('admin.akun.index') }}" class="nav-link">
                <span class="nav-icon">♙</span><span>Manajemen Akun</span>
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

    <main class="main-area">
        <header class="topbar">
            <label class="search-box">
                <span>⌕</span>
                <input type="search" id="adminTopSearch" placeholder="Cari barang, kode, atau peminjam">
            </label>
            <div class="topbar-right">
                <button class="notification-button" type="button" aria-label="Notifikasi">♧<span class="notification-dot"></span></button>
                <div class="profile">
                    <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}</div>
                    <div class="profile-info">
                        <strong>{{ auth()->user()->nama }}</strong>
                        <span>Admin Tata Usaha</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="damage-content">
           <section class="damage-heading">

    <div>
        <span class="damage-eyebrow">
            PELAPORAN KERUSAKAN
        </span>

        <h1>Laporan Kerusakan Barang</h1>

        <p>
            Laporan dibuat oleh peminjam dan selalu terhubung
            dengan peminjaman yang tercatat. Sistem hanya mencatat
            dan mengelola tindak lanjut administrasinya.
        </p>
    </div>

    <div class="damage-heading-actions">

        <span class="damage-total-pill">
            {{ $jumlahKerusakan ?? $laporan->total() }}
            laporan aktif
        </span>

        <a href="{{ route('admin.kerusakan.export', request()->query()) }}"
           class="damage-btn export-excel-btn">

            <span>↓</span>
            Download Excel

        </a>

    </div>

</section>
{{-- NAVIGASI JENIS PENCATATAN KERUSAKAN --}}
<div class="inventory-tabs">

    <a href="{{ route('admin.kerusakan.index') }}"
       class="active">
        Laporan dari Peminjam
    </a>

    <a href="{{ route('admin.kerusakan.inventaris.index') }}">
        Inventaris Barang Rusak
    </a>

</div>

            @if (session('success'))
                <div class="damage-alert success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="damage-alert error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="damage-alert error">
                    <strong>Periksa kembali data:</strong>
                    <ul style="margin:6px 0 0;padding-left:20px;">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <section class="damage-stats">
                <article class="damage-stat">
                    <div class="damage-stat-top"><span class="damage-stat-label">Menunggu ditindaklanjuti</span><span class="damage-stat-icon amber">◷</span></div>
                    <h2>{{ $jumlahMenunggu ?? $laporan->getCollection()->where('status', 'menunggu')->count() }}</h2>
                    <p>Laporan baru dari guru atau siswa</p>
                </article>
                <article class="damage-stat">
                    <div class="damage-stat-top"><span class="damage-stat-label">Sedang diproses</span><span class="damage-stat-icon">⚒</span></div>
                    <h2>{{ $jumlahDiproses ?? $laporan->getCollection()->where('status', 'diproses')->count() }}</h2>
                    <p>Sudah diperiksa Tata Usaha</p>
                </article>
                <article class="damage-stat">
                    <div class="damage-stat-top"><span class="damage-stat-label">Selesai ditangani</span><span class="damage-stat-icon">✓</span></div>
                    <h2>{{ $jumlahSelesai ?? $laporan->getCollection()->where('status', 'selesai')->count() }}</h2>
                    <p>Tercatat lengkap beserta tanggapan</p>
                </article>
                <article class="damage-stat">
                    <div class="damage-stat-top"><span class="damage-stat-label">Total laporan</span><span class="damage-stat-icon blue">♧</span></div>
                    <h2>{{ $totalLaporan ?? $laporan->total() }}</h2>
                    <p>Satu peminjaman dapat memiliki beberapa laporan</p>
                </article>
            </section>

            <form class="damage-filter" method="GET" action="{{ route('admin.kerusakan.index') }}">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="⌕  Cari barang, kode laporan, atau pelapor">
                <select name="status" aria-label="Filter status">
                    <option value="">Semua status</option>
                    <option value="menunggu" @selected(request('status') === 'menunggu')>Menunggu</option>
                    <option value="diproses" @selected(request('status') === 'diproses')>Diproses</option>
                    <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                    <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
                </select>
                <select name="jenis_laporan" aria-label="Filter jenis laporan">
                    <option value="">Semua tingkat</option>
                    <option value="saat_diterima" @selected(request('jenis_laporan') === 'saat_diterima')>Saat diterima</option>
                    <option value="selama_dipinjam" @selected(request('jenis_laporan') === 'selama_dipinjam')>Selama dipinjam</option>
                </select>
                <div class="damage-filter-actions">
                    <button class="damage-btn" type="submit">Terapkan</button>
                    <a class="damage-btn secondary" href="{{ route('admin.kerusakan.index') }}">Reset</a>
                </div>
            </form>

            <section class="damage-list-panel">
                <div class="damage-list-head">
                    <h2>Daftar laporan masuk</h2>
                    <p>Setiap laporan menampilkan peminjaman asalnya sehingga penelusuran tetap jelas.</p>
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

                            $jenisClass = match($item->tingkat_kerusakan ?? 'ringan') {
                                'sedang' => 'level-sedang',
                                'berat' => 'level-berat',
                                default => 'level-ringan',
                            };

                            $kodeLaporan = 'LK-' . optional($item->tanggal_laporan)->format('ymd') . '-' . str_pad((string)$item->id, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <article class="damage-card">
                            <div class="damage-card-top">
                                <div>
                                    <div class="damage-code">{{ $item->kode_laporan ?? $kodeLaporan }}</div>
                                    <h3>{{ $item->peminjamanDetail?->barang?->nama_barang ?? 'Barang tidak ditemukan' }}</h3>
                                    <p class="damage-description">{{ $item->keterangan ?: 'Tidak ada keterangan.' }}</p>
                                </div>
                                <div class="damage-badges">
                                    @if (!empty($item->tingkat_kerusakan))
                                        <span class="damage-badge {{ $jenisClass }}">{{ ucfirst($item->tingkat_kerusakan) }}</span>
                                    @endif
                                    <span class="damage-badge {{ $statusClass }}">
                                        @if ($item->status === 'menunggu') ◷
                                        @elseif ($item->status === 'diproses') ⚒
                                        @elseif ($item->status === 'selesai') ✓
                                        @else ! @endif
                                        {{ strtoupper($item->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="damage-meta">
                                <span>♧ Dari peminjaman {{ $item->kode_peminjaman ?? ('PJ-' . str_pad((string)($item->peminjaman_id ?? 0), 6, '0', STR_PAD_LEFT)) }}</span>
                                <span>▣ {{ $item->peminjamanDetail?->jumlah ?? 1 }} unit rusak</span>
                                <span>♙ {{ $item->peminjaman?->user?->nama ?? $item->peminjaman?->user?->name ?? 'Peminjam' }}</span>
                                <span>◷
                                    {{ $item->tanggal_laporan ? \Carbon\Carbon::parse($item->tanggal_laporan)->format('d M Y') : 'Tanggal tidak tersedia' }}
                                    @if ($item->created_at) , {{ $item->created_at->format('H:i') }} @endif
                                </span>
                                @if ($item->nomor_whatsapp)
                                    <span>WhatsApp: {{ $item->nomor_whatsapp }}</span>
                                @endif
                            </div>

                            @if ($item->catatan_admin)
                                <div class="damage-response">
                                    <strong>TANGGAPAN TATA USAHA</strong>
                                    <p>{{ $item->catatan_admin }}</p>
                                </div>
                            @endif

                            @if ($item->bukti_laporan)
                                <div style="margin-top:12px;">
                                    <a class="damage-btn secondary small" href="{{ asset('storage/' . $item->bukti_laporan) }}" target="_blank" rel="noopener">Lihat bukti foto ↗</a>
                                </div>
                            @endif

                            <div class="damage-card-bottom">
                                @if (in_array($item->status, ['menunggu', 'diproses']))
                                    <form class="damage-action-form" action="{{ route('laporan-kerusakan.status', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" required aria-label="Tindakan laporan">
                                            <option value="">Pilih tindak lanjut</option>
                                            @if ($item->status === 'menunggu')
                                                <option value="diproses">Mulai proses</option>
                                            @endif
                                            <option value="selesai">Tandai selesai</option>
                                            <option value="ditolak">Tolak laporan</option>
                                        </select>
                                        <textarea name="catatan_admin" rows="2" placeholder="Tanggapan Tata Usaha (opsional)">{{ old('catatan_admin', $item->catatan_admin) }}</textarea>
                                        <button type="submit" class="damage-btn" onclick="return confirm('Simpan tindak lanjut laporan ini?')">Tindak lanjut →</button>
                                    </form>
                                @else
                                    <span class="damage-btn secondary small">Laporan {{ $item->status }}</span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="damage-empty">
                            <strong>Belum ada laporan kerusakan</strong>
                            Laporan yang dikirim guru atau siswa akan muncul di sini.
                        </div>
                    @endforelse
                </div>

                @if (method_exists($laporan, 'links'))
                    <div class="damage-pagination">{{ $laporan->withQueryString()->links() }}</div>
                @endif
            </section>

            <footer class="dashboard-footer">
                <span>SISKO · Sistem Peminjaman Barang Tata Usaha</span>
                <span>Dashboard Administrator</span>
            </footer>
        </div>
    </main>
</div>

<script>
    // Pencarian topbar membantu memfilter kartu laporan yang sedang tampil.
    document.getElementById('adminTopSearch')?.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        document.querySelectorAll('.damage-card').forEach(function (card) {
            card.style.display = card.textContent.toLowerCase().includes(keyword) ? '' : 'none';
        });
    });
</script>
</body>
</html>
