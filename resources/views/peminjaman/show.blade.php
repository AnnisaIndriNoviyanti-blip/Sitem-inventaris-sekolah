
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Peminjaman | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/peminjaman.css') }}">
    <link rel="stylesheet" href="{{ asset('css/detail-peminjaman.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="student-app-layout">

    <!-- SIDEBAR PEMINJAM -->
    <aside class="student-sidebar">
        <a href="{{ auth()->user()->jenis_akun === 'kelas'
            ? route('kelas.dashboard')
            : route('organisasi.dashboard') }}"
           class="student-brand">

            <div class="student-brand-icon">S</div>
            <div class="student-brand-text">
                <strong>SISKO</strong>
                <span>SISTEM PEMINJAMAN BARANG</span>
            </div>
        </a>

        <div class="student-sidebar-label">MENU UTAMA</div>

        <nav class="student-nav">
            <a href="{{ auth()->user()->jenis_akun === 'kelas'
                ? route('kelas.dashboard')
                : route('organisasi.dashboard') }}"
               class="student-nav-link">
                <span class="student-nav-icon">▦</span>
                <span>Beranda</span>
            </a>

            <a href="{{ route('peminjaman.create') }}"
               class="student-nav-link">
                <span class="student-nav-icon">▤</span>
                <span>Katalog Barang TU</span>
            </a>

            <a href="{{ route('peminjaman.index') }}"
               class="student-nav-link active">
                <span class="student-nav-icon">▧</span>
                <span>Peminjaman Saya</span>
            </a>

            <a href="{{ route('laporan-kerusakan.index') }}"
               class="student-nav-link">
                <span class="student-nav-icon">♧</span>
                <span>Laporan Kerusakan</span>
            </a>
        </nav>

        <div class="student-sidebar-bottom">
            <div class="student-account-card">
                <span class="student-account-label">AKUN AKTIF</span>

                <div class="student-account-user">
                    <div class="student-account-avatar">
                        {{ strtoupper(substr(auth()->user()->nama ?? 'S', 0, 1)) }}
                    </div>

                    <div class="student-account-info">
                        <strong>{{ auth()->user()->nama ?? 'Pengguna SISKO' }}</strong>
                        <span>{{ ucfirst(auth()->user()->jenis_akun ?? 'Pengguna') }}</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="student-logout" type="submit">
                    <span>⇥</span> Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <div class="student-main-area">

        <header class="student-topbar">
            <div class="student-topbar-title">
                <span class="eyebrow">SISKO · PEMINJAMAN BARANG</span>
                <strong>Detail Peminjaman</strong>
            </div>

            <div class="student-profile">
                <div class="student-profile-avatar">
                    {{ strtoupper(substr(auth()->user()->nama ?? 'S', 0, 1)) }}
                </div>

                <div class="student-profile-info">
                    <strong>{{ auth()->user()->nama }}</strong>
                    <span>{{ ucfirst(auth()->user()->jenis_akun ?? 'Pengguna') }}</span>
                </div>
            </div>
        </header>

        <main class="page">
            <section class="heading">
                <div>
                    <span class="eyebrow">RIWAYAT PEMINJAMAN</span>
                    <h1>Detail Peminjaman</h1>
                    <p class="muted">
                        Informasi lengkap pengajuan dan barang yang dipinjam.
                    </p>
                </div>

                <a href="{{ route('peminjaman.index') }}"
                   class="button button-outline">
                    ← Kembali
                </a>
            </section>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- KOMPONEN DETAIL -->
            @include('peminjaman.partials.detail-modal', [
                'peminjaman' => $peminjaman
            ])
        </main>
    </div>
</div>

</body>
</html>
