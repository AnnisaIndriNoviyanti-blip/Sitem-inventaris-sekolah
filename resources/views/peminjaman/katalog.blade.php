
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Katalog Barang TU | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kelas/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/peminjaman-katalog.css') }}">
    <link rel="stylesheet" href="{{ asset('css/peminjaman.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
          rel="stylesheet">
</head>

<body>

<div class="kelas-layout">

    {{-- SIDEBAR --}}
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
   class="kelas-nav-link active">

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



    {{-- MAIN CONTENT --}}
    <main class="kelas-main">

        {{-- TOPBAR --}}
        <header class="kelas-topbar">

            <form action="{{ route('peminjaman.katalog') }}"
                  method="GET"
                  class="kelas-search">

                <span>⌕</span>

                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari barang, kode, atau spesifikasi">

            </form>

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


        {{-- KONTEN KATALOG --}}
        <div class="kelas-content katalog-content">

            {{-- JUDUL DAN TOTAL BARANG --}}
            <section class="kelas-heading katalog-heading">

                <div>

                    <span class="kelas-eyebrow">
                        KATALOG
                    </span>

                    <h1>Barang Tata Usaha</h1>

                    <p>
                        Seluruh barang pada katalog ini berada dalam
                        pengelolaan Tata Usaha sekolah.
                        Pilih barang untuk mengajukan peminjaman.
                    </p>

                </div>

                <span class="katalog-total">
                    {{ number_format($totalBarang, 0, ',', '.') }}
                    JENIS BARANG
                </span>

            </section>


            {{-- PENCARIAN DAN FILTER --}}
            <form action="{{ route('peminjaman.katalog') }}"
                  method="GET"
                  class="katalog-filter">

                {{-- PENCARIAN --}}
                <label class="katalog-search">

                    <span>⌕</span>

                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama atau kode barang">

                </label>


                {{-- FILTER KATEGORI --}}
                <select name="kategori" aria-label="Filter kategori">

                    <option value="">
                        Semua kategori
                    </option>

                    @foreach ($kategori as $jenis)

    <option
        value="{{ $jenis }}"
        @selected(request('kategori') === $jenis)>

        {{ ucwords(str_replace('_', ' ', $jenis)) }}

    </option>

@endforeach

                </select>


                {{-- FILTER KETERSEDIAAN --}}
                <label class="katalog-toggle">

    <input
        type="checkbox"
        name="tersedia"
        value="1"
        @checked(request()->boolean('tersedia'))
        onchange="this.form.submit()">

    <span class="toggle-track"></span>

    <span>Hanya yang tersedia</span>

</label>


                <button type="submit" class="filter-submit">
                    Terapkan
                </button>

            </form>


            {{-- INFORMASI HASIL PENCARIAN --}}
            @if (request()->hasAny(['search', 'kategori', 'tersedia']))

                <div class="katalog-result-note">

                    Menampilkan {{ $barang->total() }} hasil

                    <a href="{{ route('peminjaman.katalog') }}">
                        Reset filter
                    </a>

                </div>

            @endif


            {{-- DAFTAR KARTU BARANG --}}
            <section class="katalog-grid">

                @forelse ($barang as $item)

                    @php

                        // Menentukan status ketersediaan barang
                        $isAvailable =
                            $item->status === 'tersedia' &&
                            $item->kondisi === 'baik';


                        // Nama kategori
                        $jenisLabel = [
                            'paket_proyektor' => 'Paket Proyektor',
                            'laptop' => 'Laptop',
                            'speaker' => 'Speaker'
                        ][$item->jenis_barang]
                            ?? ucwords(str_replace(
                                '_',
                                ' ',
                                $item->jenis_barang ?? 'Barang'
                            ));


                        // Ikon berdasarkan kategori
                        $icon = [
                            'paket_proyektor' => '▣',
                            'laptop' => '▱',
                            'speaker' => '♫'
                        ][$item->jenis_barang] ?? '▤';


                        // Label kondisi
                        $kondisiLabel = [
                            'baik' => 'Baik',
                            'rusak_ringan' => 'Rusak ringan',
                            'rusak_berat' => 'Rusak berat'
                        ][$item->kondisi] ?? ucfirst($item->kondisi ?? 'Belum diketahui');

                    @endphp


                    <article class="katalog-card">

                        {{-- IDENTITAS BARANG --}}
                        <div class="katalog-card-top">

                            <div class="katalog-item-icon">
                                {{ $icon }}
                            </div>

                            <div class="katalog-item-title">

                                <h2>
                                    {{ $item->nama_barang }}
                                </h2>

                                <span>
                                    {{ $item->kode_barang }}
                                </span>

                            </div>

                        </div>


                        {{-- INFORMASI KETERSEDIAAN --}}
                        <div class="katalog-stock">

                            <div class="stock-label">

                                <span>
                                    Ketersediaan
                                </span>

                                <strong>
                                    {{ $isAvailable
                                        ? 'Tersedia'
                                        : 'Tidak tersedia' }}
                                </strong>

                            </div>

                            <div class="stock-track">

                                <span class="{{ $isAvailable
                                    ? 'stock-active'
                                    : 'stock-inactive' }}">
                                </span>

                            </div>

                        </div>


                        {{-- BADGE STATUS DAN KATEGORI --}}
                        <div class="katalog-badges">

                            <span class="availability-badge {{ $isAvailable
                                ? 'available'
                                : 'unavailable' }}">

                                <span>
                                    {{ $isAvailable ? '✓' : '!' }}
                                </span>

                                {{ $isAvailable
                                    ? 'TERSEDIA'
                                    : 'TIDAK TERSEDIA' }}

                            </span>

                            <span class="category-badge">
                                {{ $jenisLabel }}
                            </span>

                        </div>


                        {{-- SPESIFIKASI BARANG --}}
                        @if ($item->spesifikasi)

                            <p class="katalog-spec">
                                {{ $item->spesifikasi }}
                            </p>

                        @else

                            <p class="katalog-spec muted">
                                Informasi spesifikasi belum tersedia.
                            </p>

                        @endif


                        {{-- KONDISI DAN TOMBOL PEMINJAMAN --}}
                        <div class="katalog-card-bottom">

                            <span class="condition-label">

                                <span>Lokasi dan kondisi barang</span>

                                <strong>{{ $kondisiLabel }}</strong>

                            </span>


                            {{-- BARANG TERSEDIA --}}
                            @if ($isAvailable)

                                <a href="{{ route('peminjaman.index') }}"
                                    class="katalog-action">

                                    <span>▤</span>
                                    Ajukan peminjaman

                                </a>


                            {{-- BARANG TIDAK TERSEDIA --}}
                            @else

                                <button
                                    type="button"
                                    class="katalog-action disabled"
                                    disabled>

                                    Belum tersedia

                                </button>

                            @endif

                        </div>

                    </article>


                {{-- JIKA DATA BARANG KOSONG --}}
                @empty

                    <div class="katalog-empty">

                        <div>▤</div>

                        <h2>
                            {{ $totalBarang === 0
                                ? 'Belum ada barang di katalog'
                                : 'Barang tidak ditemukan' }}
                        </h2>

                        <p>

                            {{ $totalBarang === 0
                                ? 'Data barang akan muncul di sini setelah Tata Usaha menambahkan inventaris.'
                                : 'Coba ubah kata kunci atau filter kategori yang digunakan.' }}

                        </p>

                        @if ($totalBarang > 0)

                            <a href="{{ route('peminjaman.katalog') }}">
                                Tampilkan semua barang
                            </a>

                        @endif

                    </div>

                @endforelse

            </section>


            {{-- PAGINATION --}}
            @if ($barang->hasPages())

                <div class="katalog-pagination">
                    {{ $barang->links() }}
                </div>

            @endif

        </div>

    </main>

</div>

</body>
</html>
