
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/peminjaman.css') }}">
</head>
<body>
<div class="app-layout">

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
                <span class="nav-badge">{{ $menungguVerifikasi }}</span>
            </a>

            <a href="{{ route('admin.pengembalian.index') }}"
               class="nav-link active">
                <span class="nav-icon">↻</span>
                <span>Pengembalian</span>
            </a>

            <a href="{{ url('/admin/kerusakan') }}" class="nav-link">
                <span class="nav-icon">♧</span>
                <span>Laporan Kerusakan</span>
                <span class="nav-badge warning">{{ $jumlahKerusakan ?? 0 }}</span>
            </a>

            <a href="{{ url('/admin/riwayat') }}" class="nav-link">
                <span class="nav-icon">◷</span>
                <span>Riwayat & Rekap</span>
            </a>

            <a href="{{ route('admin.akun.index') }}" class="nav-link">
                <span class="nav-icon">♙</span>
                <span>Manajemen Akun</span>
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
                    <span>⇥</span> Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="main-area">
        <header class="topbar">
            <div class="profile">
                <div class="profile-avatar">
                    {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                </div>
                <div class="profile-info">
                    <strong>{{ auth()->user()->nama }}</strong>
                    <span>Admin Tata Usaha</span>
                </div>
            </div>
        </header>

        <div class="dashboard-content">
            

<section class="page-heading">
    <div>
        <span class="eyebrow">TATA USAHA</span>
        <h1>Pengembalian Barang</h1>
        <p>
            Daftar peminjaman yang sudah dikonfirmasi dikembalikan.
        </p>
    </div>

    <div class="pengembalian-actions">
        <a href="{{ route('admin.peminjaman.export') }}"
           class="detail-link">
            📊 Download Excel
        </a>

        <a href="{{ route('admin.peminjaman.cetak') }}"
           class="detail-link"
           target="_blank">
            🖨 Cetak Buku
        </a>
    </div>
</section>


            @if (session('success'))
                <p>{{ session('success') }}</p>
            @endif

            @if ($errors->any())
                <div>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <section class="panel">
                <form method="GET"
                      action="{{ route('admin.pengembalian.index') }}">
                    <input type="search"
                           name="cari"
                           value="{{ request('cari') }}"
                           placeholder="Cari nama peminjam atau barang">

                    <button type="submit">Cari</button>

                    <a href="{{ route('admin.pengembalian.index') }}">
                        Reset
                    </a>
                </form>

                <div style="overflow-x:auto; margin-top:20px;">
                    <table style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="text-align:left;padding:12px;">Peminjam</th>
                                <th style="text-align:left;padding:12px;">Barang</th>
                                <th style="text-align:left;padding:12px;">Tanggal Pinjam</th>
                                <th style="text-align:left;padding:12px;">Rencana Kembali</th>
                                <th style="text-align:left;padding:12px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($peminjaman as $item)
                                <tr>
                                    <td style="padding:12px;">
                                        <strong>{{ $item->nama_peminjam }}</strong>
                                        <br>
                                        <small>
                                            {{ $item->user->nama ?? '-' }}
                                            ({{ ($item->user->jenis_akun ?? '') === 'kelas'
                                                ? 'Kelas'
                                                : match($item->user?->organisasi?->jenis ?? '') {
                                                    'organisasi' => 'Organisasi',
                                                    'ekskul' => 'Ekskul',
                                                    default => ucfirst($item->user->jenis_akun ?? '-'),
                                                }
                                            }})
                                        </small>
                                    </td>

                                    <td style="padding:12px;">
                                        @forelse ($item->detail as $detail)
                                            {{ $detail->barang->nama_barang ?? 'Barang dihapus' }}
                                            <br>
                                        @empty
                                            -
                                        @endforelse
                                    </td>

                                    <td style="padding:12px;">
                                        {{ $item->tanggal_pinjam?->format('d/m/Y') ?? '-' }}
                                    </td>

                                    <td style="padding:12px;">
                                        {{ $item->tanggal_rencana_kembali?->format('d/m/Y') ?? '-' }}
                                    </td>

                                    <td style="padding:12px;">
                                        {{ $item->status === 'dikembalikan'
                                            ? 'Sudah Dikembalikan'
                                            : ucfirst($item->status) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        style="padding:20px;text-align:center;">
                                        Belum ada barang yang dikembalikan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:20px;">
                    {{ $peminjaman->links() }}
                </div>
            </section>
        </div>
    </main>
</div>
</body>
</html>
