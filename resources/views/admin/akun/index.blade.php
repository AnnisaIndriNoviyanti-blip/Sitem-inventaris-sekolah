
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Manajemen Akun | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/akun/manajemen_akun.css') }}?v=2">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>

<body>
<div class="app-layout">

    {{-- SIDEBAR --}}
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

            <a href="{{ route('admin.kerusakan.index') }}" class="nav-link">
                <span class="nav-icon">♧</span>
                <span>Laporan Kerusakan</span>
                <span class="nav-badge warning">{{ $jumlahKerusakan ?? 0 }}</span>
            </a>

            <a href="{{ url('/admin/riwayat') }}" class="nav-link">
                <span class="nav-icon">◷</span>
                <span>Riwayat & Rekap</span>
            </a>

            <a href="{{ route('admin.akun.index') }}"
               class="nav-link {{ request()->routeIs('admin.akun.*') ? 'active' : '' }}">
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

    {{-- AREA UTAMA --}}
    <main class="main-area">

        {{-- TOPBAR --}}
        <header class="topbar">
            <label class="search-box">
                <span>⌕</span>
                <input type="search"
                       id="akunSearch"
                       placeholder="Cari nama akun atau username">
            </label>

            <div class="topbar-right">
                <button class="notification-button"
                        type="button"
                        aria-label="Notifikasi">
                    ♧
                    <span class="notification-dot"></span>
                </button>

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

        {{-- KONTEN MANAJEMEN AKUN --}}
        <div class="dashboard-content akun-content">

            <section class="page-heading">
                <div>
                    <span class="eyebrow">ADMINISTRATOR</span>
                    <h1>Manajemen Akun</h1>
                    <p>
                        Kelola akun kelas dan organisasi sekolah.
                    </p>
                </div>

                <a class="primary-button"
                   href="{{ route('admin.akun.create') }}">
                    + Tambah Akun
                </a>
            </section>

            {{-- NOTIFIKASI --}}
            @if(session('success'))
                <div class="alert success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert error" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- TABEL AKUN --}}
            <section class="panel akun-panel">
                <div class="panel-header">
                    <div>
                        <h2>Daftar Akun</h2>
                        <p>Informasi akun pengguna yang terdaftar di SISKO.</p>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table id="akunTable">
                        <thead>
                            <tr>
                                <th>Nama Akun</th>
                                <th>Username</th>
                                <th>Password Aktif</th>
                                <th>Jenis</th>
                                <th>Kelas / Organisasi / Ekskul</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($akun as $user)
                                <tr>
                                    <td>{{ $user->nama }}</td>

                                    <td>{{ $user->username }}</td>
                                    <td>
    @if(in_array($user->jenis_akun, ['kelas', 'organisasi'], true))

        @if($user->password_aktif)

            <code>{{ $user->password_aktif }}</code>

            <small>
                Diperbarui:
                {{ $user->password_rotated_at?->format('d-m-Y H:i') ?? '—' }}
            </small>

        @else

            <span>Belum tersedia</span>

            <form
                action="{{ route('admin.akun.terbitkan-password', $user->id) }}"
                method="POST"
                onsubmit="return confirm('Terbitkan password baru?')"
            >
                @csrf

                <button type="submit">
                    Terbitkan Password
                </button>
            </form>

        @endif

    @else
        —
    @endif
</td>

                                    <td>
                                        {{ $user->role === 'admin_tu'
                                            ? 'Admin TU'
                                            : ($user->jenis_akun ?? $user->role) }}
                                    </td>

                                    <td>
                                        @if($user->role === 'admin_tu')
                                            —
                                        @elseif($user->jenis_akun === 'kelas')
                                            {{ $user->kelas?->nama_kelas ?? '—' }}
                                        @elseif($user->jenis_akun === 'organisasi')
                                            {{ ucfirst($user->organisasi?->jenis ?? '—') }}
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td>
                                        <span class="status {{ $user->status === 'aktif'
                                            ? 'status-aktif'
                                            : 'status-nonaktif' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="aksi">
                                            <a class="btn-edit"
                                               href="{{ route('admin.akun.edit', $user->id) }}">
                                                Edit
                                            </a>

                                            <form
                                                action="{{ route('admin.akun.status', $user->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('{{ $user->status === 'aktif'
                                                    ? 'Nonaktifkan'
                                                    : 'Aktifkan kembali' }} akun ini?')">
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="btn-status {{ $user->status === 'aktif'
                                                        ? 'btn-nonaktifkan'
                                                        : 'btn-aktifkan' }}"
                                                    {{ $user->role === 'admin_tu' ? 'disabled' : '' }}>
                                                    {{ $user->role === 'admin_tu'
                                                        ? 'Admin'
                                                        : ($user->status === 'aktif'
                                                            ? 'Nonaktifkan'
                                                            : 'Aktifkan') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="akun-empty">
                                        Belum ada data akun.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper">
    {{ $akun->onEachSide(1)->links() }}
</div>
            </section>

            <footer class="dashboard-footer">
                <span>SISKO · Sistem Peminjaman Barang Tata Usaha</span>
                <span>Manajemen Akun</span>
            </footer>

        </div>
    </main>
</div>

<script>
    const akunSearch = document.getElementById('akunSearch');

    akunSearch?.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();

        document.querySelectorAll('#akunTable tbody tr').forEach(function (row) {
            row.style.display = row.textContent.toLowerCase().includes(keyword)
                ? ''
                : 'none';
        });
    });
</script>
</body>
</html>
