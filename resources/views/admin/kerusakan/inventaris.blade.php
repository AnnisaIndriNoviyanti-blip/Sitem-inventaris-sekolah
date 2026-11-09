
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inventaris Barang Rusak | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/kerusakan.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/inventaris-rusak.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
          rel="stylesheet">
</head>

<body>

<div class="app-layout">

    {{-- SIDEBAR --}}
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
            </a>

            <a href="{{ route('admin.pengembalian.index') }}" class="nav-link">
                <span class="nav-icon">↻</span>
                <span>Pengembalian</span>
            </a>

            <a href="{{ route('admin.kerusakan.index') }}" class="nav-link active">
                <span class="nav-icon">♧</span>
                <span>Laporan Kerusakan</span>
            </a>

            <a href="{{ route('admin.riwayat.index') }}" class="nav-link">
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


    {{-- AREA UTAMA --}}
    <main class="main-area">

        <header class="topbar">

            <div class="inventory-top-title">
                Pengelolaan Inventaris Kerusakan
            </div>

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


        <div class="dashboard-content inventory-damage-content">

            {{-- JUDUL --}}
            <section class="damage-heading">

                <div>
                    <span class="damage-eyebrow">
                        INVENTARIS TU
                    </span>

                    <h1>Inventaris Barang Rusak</h1>

                    <p>
                        Catat barang inventaris yang mengalami kerusakan
                        dan tidak dapat digunakan. Data ini dikelola
                        langsung oleh Admin Tata Usaha.
                    </p>
                </div>
                
<div class="inventory-export-actions">

    <a href="{{ route('admin.kerusakan.inventaris.export-pdf') }}"
       class="inventory-export-btn">

        <span>↓</span>
        Download Laporan Kerusakan PDF

    </a>

</div>

<div class="inventory-export-actions">

    <a href="{{ route('admin.kerusakan.penanganan.export-pdf') }}"
       class="inventory-export-btn">

        <span>↓</span>
        Download Laporan Penanganan PDF

    </a>

</div>


                

                <div class="inventory-heading-actions">

                    <a href="{{ route('admin.kerusakan.inventaris.export', request()->query()) }}"
                       class="damage-btn inventory-export-btn">
                        ↓ Download Excel
                    </a>

                    <a href="{{ route('admin.kerusakan.inventaris.create') }}"
                       class="damage-btn inventory-add-btn">
                        + Tambah Barang Rusak
                    </a>

                </div>
                

            </section>


            {{-- TAB KERUSAKAN --}}
            <div class="inventory-tabs">

                <a href="{{ route('admin.kerusakan.index') }}">
                    Laporan dari Peminjam
                </a>

                <a href="{{ route('admin.kerusakan.inventaris.index') }}"
                   class="active">
                    Inventaris Barang Rusak
                </a>

            </div>


            {{-- NOTIFIKASI --}}
            @if (session('success'))
                <div class="inventory-alert success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="inventory-alert error">
                    {{ session('error') }}
                </div>
            @endif


            {{-- STATISTIK --}}
            <section class="inventory-stats">

                <article class="inventory-stat">
                    <span>Total barang rusak aktif</span>
                    <strong>{{ $totalRusak }}</strong>
                </article>

                <article class="inventory-stat">
                    <span>Rusak ringan</span>
                    <strong>{{ $rusakRingan }}</strong>
                </article>

                <article class="inventory-stat">
                    <span>Rusak berat</span>
                    <strong>{{ $rusakBerat }}</strong>
                </article>

            </section>


            {{-- FILTER --}}
            <form action="{{ route('admin.kerusakan.inventaris.index') }}"
                  method="GET"
                  class="inventory-filter">

                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama atau kode barang">

                <select name="tingkat_kerusakan">
                    <option value="">Semua tingkat kerusakan</option>

                    <option value="rusak_ringan"
                        @selected(request('tingkat_kerusakan') === 'rusak_ringan')>
                        Rusak ringan
                    </option>

                    <option value="rusak_berat"
                        @selected(request('tingkat_kerusakan') === 'rusak_berat')>
                        Rusak berat
                    </option>
                </select>

                <select name="status_penanganan">
                    <option value="">Semua status penanganan</option>

                    <option value="belum_diperbaiki"
                        @selected(request('status_penanganan') === 'belum_diperbaiki')>
                        Belum diperbaiki
                    </option>

                    <option value="sedang_diperbaiki"
                        @selected(request('status_penanganan') === 'sedang_diperbaiki')>
                        Sedang diperbaiki
                    </option>

                    <option value="selesai"
                        @selected(request('status_penanganan') === 'selesai')>
                        Selesai
                    </option>
                </select>

                <button type="submit" class="damage-btn">
                    Terapkan
                </button>

                <a href="{{ route('admin.kerusakan.inventaris.index') }}"
                   class="damage-btn secondary">
                    Reset
                </a>

            </form>


            {{-- DAFTAR BARANG RUSAK --}}
            <section class="damage-list-panel">

                <div class="damage-list-head">
                    <h2>Daftar inventaris barang rusak</h2>

                    <p>
                        Data barang diambil langsung dari inventaris
                        Tata Usaha dan terhubung dengan bukti kerusakan.
                    </p>
                </div>

                <div class="inventory-damage-list">

                    @forelse ($inventarisRusak as $item)

                        <article class="inventory-damage-card">

                            <div class="inventory-damage-top">

                                <div>
                                    <span class="inventory-code">
                                        {{ $item->barang?->kode_barang ?? '-' }}
                                    </span>

                                    <h3>
                                        {{ $item->barang?->nama_barang ?? 'Barang tidak ditemukan' }}
                                    </h3>

                                    <span class="inventory-category">
                                        {{ $item->barang?->jenis_barang
                                            ? ucwords(str_replace('_', ' ', $item->barang->jenis_barang))
                                            : 'Kategori tidak tersedia' }}
                                    </span>
                                </div>

                                <span class="inventory-level
                                    {{ $item->tingkat_kerusakan === 'rusak_berat'
                                        ? 'heavy'
                                        : 'light' }}">

                                    {{ $item->tingkat_kerusakan === 'rusak_berat'
                                        ? 'RUSAK BERAT'
                                        : 'RUSAK RINGAN' }}

                                </span>

                            </div>

                            <div class="inventory-damage-description">
                                <strong>Catatan kerusakan</strong>
                                <p>{{ $item->catatan }}</p>
                            </div>

                            <div class="inventory-damage-meta">

                                <span>
                                    Tanggal:
                                    {{ $item->tanggal_kerusakan->format('d M Y') }}
                                </span>

                                <span>
                                    Dicatat oleh:
                                    {{ $item->admin?->nama ?? 'Admin TU' }}
                                </span>

                                <span>
                                    Status:
                                    {{ ucwords(str_replace('_', ' ', $item->status_penanganan)) }}
                                </span>

                            </div>

                            @if ($item->foto_bukti)
                                <div class="inventory-photo">

                                    <a href="{{ asset('storage/' . $item->foto_bukti) }}"
                                       target="_blank"
                                       rel="noopener">

                                        <img
                                            src="{{ asset('storage/' . $item->foto_bukti) }}"
                                            alt="Bukti kerusakan {{ $item->barang?->nama_barang }}"
                                            loading="lazy">

                                    </a>

                                    <span>Lihat bukti foto ↗</span>

                                </div>
                            @endif

                            
{{-- FOTO SETELAH PERBAIKAN --}}
@if ($item->foto_perbaikan)
    <div class="inventory-repair-photo">

        <div class="repair-photo-title">
            <strong>✓ Foto setelah diperbaiki</strong>

            @if ($item->tanggal_perbaikan)
                <span>
                    {{ $item->tanggal_perbaikan->format('d M Y') }}
                </span>
            @endif
        </div>

        <a href="{{ asset('storage/' . $item->foto_perbaikan) }}"
           target="_blank"
           rel="noopener">

            <img
                src="{{ asset('storage/' . $item->foto_perbaikan) }}"
                alt="Foto setelah perbaikan {{ $item->barang?->nama_barang }}"
                loading="lazy">

        </a>

    </div>
@endif


{{-- FORM UPDATE STATUS --}}
@if ($item->status_penanganan !== 'selesai')

    <form
        action="{{ route('admin.kerusakan.inventaris.penanganan', $item->id) }}"
        method="POST"
        enctype="multipart/form-data"
        class="inventory-repair-form">

        @csrf
        @method('PATCH')

        <div class="repair-form-heading">
            <strong>Update penanganan barang</strong>

            <span>
                Ubah status sesuai perkembangan perbaikan.
            </span>
        </div>

        <div class="repair-form-fields">

            <div class="repair-status-field">

                <label>Status penanganan</label>

                <select
                    name="status_penanganan"
                    class="repair-status-select"
                    required>

                    <option
                        value="belum_diperbaiki"
                        @selected($item->status_penanganan === 'belum_diperbaiki')>
                        Belum Diperbaiki
                    </option>

                    <option
                        value="sedang_diperbaiki"
                        @selected($item->status_penanganan === 'sedang_diperbaiki')>
                        Sedang Diperbaiki
                    </option>

                    <option value="selesai">
                        Sudah Diperbaiki
                    </option>

                </select>

            </div>

            <div class="repair-photo-field">

                <label>Foto kondisi setelah diperbaiki</label>

                <input
                    type="file"
                    name="foto_perbaikan"
                    accept=".jpg,.jpeg,.png,image/jpeg,image/png">

                <small>
                    Wajib diunggah ketika memilih status
                    Sudah Diperbaiki. Maksimal 5 MB.
                </small>

            </div>

        </div>

        @error('status_penanganan')
            <small class="form-error">{{ $message }}</small>
        @enderror

        @error('foto_perbaikan')
            <small class="form-error">{{ $message }}</small>
        @enderror

        <div class="repair-form-footer">

            <span class="repair-note">
                Foto awal tetap tersimpan sebagai riwayat.
            </span>

            <button type="submit"
                    class="damage-btn inventory-add-btn"
                    onclick="return confirm('Simpan perubahan status penanganan barang?')">

                Simpan Perubahan

            </button>

        </div>

    </form>

@else

    <div class="inventory-repair-complete">

        <div>
            <strong>✓ Perbaikan selesai</strong>

            <p>
                Barang sudah kembali dalam kondisi baik
                dan tersedia untuk dipinjam.
            </p>
        </div>

        <span class="repair-available-badge">
            TERSEDIA
        </span>

    </div>

@endif

                        </article>

                    @empty

                        <div class="damage-empty">
                            <strong>Belum ada inventaris barang rusak</strong>
                            <p>
                                Tambahkan barang rusak melalui tombol
                                Tambah Barang Rusak.
                            </p>
                        </div>

                    @endforelse

                </div>

                <div class="damage-pagination">
                    {{ $inventarisRusak->links() }}
                </div>

            </section>


            <footer class="dashboard-footer">
                <span>SISKO · Sistem Peminjaman Barang Tata Usaha</span>
                <span>Inventaris Barang Rusak</span>
            </footer>

        </div>

    </main>

</div>


<script>
document.querySelectorAll('.inventory-repair-form').forEach(form => {

    const statusSelect = form.querySelector(
        '[name="status_penanganan"]'
    );
    
<div class="repair-status-field">

    <label>Tindakan perbaikan</label>

    <textarea
        name="tindakan_perbaikan"
        rows="3"
        placeholder="Contoh: Mengganti kabel konektor dan memeriksa sambungan...">{{ old('tindakan_perbaikan', $item->tindakan_perbaikan) }}</textarea>

</div>

<div class="repair-status-field">

    <label>Hasil pemeriksaan</label>

    <textarea
        name="hasil_perbaikan"
        rows="3"
        placeholder="Contoh: Barang sudah berfungsi normal setelah pengujian...">{{ old('hasil_perbaikan', $item->hasil_perbaikan) }}</textarea>

</div>


    const fotoInput = form.querySelector(
        '[name="foto_perbaikan"]'
    );

    function updateFotoRequirement() {
        if (statusSelect.value === 'selesai') {
            fotoInput.required = true;
        } else {
            fotoInput.required = false;
        }
    }

    statusSelect.addEventListener(
        'change',
        updateFotoRequirement
    );

    updateFotoRequirement();

});
</script>


</body>
</html>
