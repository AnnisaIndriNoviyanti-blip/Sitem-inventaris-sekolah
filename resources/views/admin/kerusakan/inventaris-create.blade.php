
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Barang Rusak | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/inventaris-rusak.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
          rel="stylesheet">
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


    <main class="main-area">

        <header class="topbar">
            <div class="inventory-top-title">
                Tambah Inventaris Barang Rusak
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

            <section class="damage-heading">
                <div>
                    <span class="damage-eyebrow">INVENTARIS TU</span>

                    <h1>Tambah Barang Rusak</h1>

                    <p>
                        Pilih barang yang sudah terdaftar dalam inventaris,
                        kemudian masukkan tingkat kerusakan, catatan,
                        dan bukti foto.
                    </p>
                </div>
            </section>


            <div class="inventory-tabs">
                <a href="{{ route('admin.kerusakan.index') }}">
                    Laporan dari Peminjam
                </a>

                <a href="{{ route('admin.kerusakan.inventaris.index') }}"
                   class="active">
                    Inventaris Barang Rusak
                </a>
            </div>


            <section class="inventory-form-panel">

                <div class="inventory-form-heading">
                    <h2>Formulir pencatatan kerusakan</h2>
                    <p>
                        Informasi barang akan diambil otomatis
                        dari data Barang Tata Usaha.
                    </p>
                </div>


                <form
                    action="{{ route('admin.kerusakan.inventaris.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="inventory-damage-form">

                    @csrf


                    {{-- PILIH BARANG --}}
                    <div class="inventory-form-group">

                        <label for="barang_tu_id">
                            Pilih barang inventaris
                            <span>*</span>
                        </label>

                        <select
                            name="barang_tu_id"
                            id="barang_tu_id"
                            required>

                            <option value="">
                                -- Pilih barang dari inventaris --
                            </option>

                            @foreach ($barang as $item)

                                <option
                                    value="{{ $item->id }}"
                                    @selected(old('barang_tu_id') == $item->id)>

                                    {{ $item->kode_barang }}
                                    -
                                    {{ $item->nama_barang }}
                                    ({{ ucfirst(str_replace('_', ' ', $item->status)) }})

                                </option>

                            @endforeach

                        </select>

                        @error('barang_tu_id')
                            <small class="form-error">{{ $message }}</small>
                        @enderror

                        @if ($barang->isEmpty())
                            <small class="form-help">
                                Tidak ada barang yang dapat dipilih.
                                Barang yang sedang dipinjam atau masih
                                memiliki pencatatan kerusakan aktif
                                tidak ditampilkan.
                            </small>
                        @else
                            <small class="form-help">
                                Barang diambil langsung dari inventaris
                                yang sudah terdaftar.
                            </small>
                        @endif

                    </div>


                    {{-- TANGGAL DAN TINGKAT --}}
                    <div class="inventory-form-row">

                        <div class="inventory-form-group">

                            <label for="tanggal_kerusakan">
                                Tanggal kerusakan
                                <span>*</span>
                            </label>

                            <input
                                type="date"
                                name="tanggal_kerusakan"
                                id="tanggal_kerusakan"
                                value="{{ old('tanggal_kerusakan', now()->format('Y-m-d')) }}"
                                required>

                            @error('tanggal_kerusakan')
                                <small class="form-error">{{ $message }}</small>
                            @enderror

                        </div>


                        <div class="inventory-form-group">

                            <label for="tingkat_kerusakan">
                                Tingkat kerusakan
                                <span>*</span>
                            </label>

                            <select
                                name="tingkat_kerusakan"
                                id="tingkat_kerusakan"
                                required>

                                <option value="">
                                    -- Pilih tingkat kerusakan --
                                </option>

                                <option
                                    value="rusak_ringan"
                                    @selected(old('tingkat_kerusakan') === 'rusak_ringan')>
                                    Rusak Ringan
                                </option>

                                <option
                                    value="rusak_berat"
                                    @selected(old('tingkat_kerusakan') === 'rusak_berat')>
                                    Rusak Berat
                                </option>

                            </select>

                            @error('tingkat_kerusakan')
                                <small class="form-error">{{ $message }}</small>
                            @enderror

                        </div>

                    </div>


                    {{-- CATATAN --}}
                    <div class="inventory-form-group">

                        <label for="catatan">
                            Catatan kerusakan
                            <span>*</span>
                        </label>

                        <textarea
                            name="catatan"
                            id="catatan"
                            rows="5"
                            maxlength="5000"
                            placeholder="Jelaskan bagian yang rusak, kondisi barang, dan keterangan lainnya..."
                            required>{{ old('catatan') }}</textarea>

                        @error('catatan')
                            <small class="form-error">{{ $message }}</small>
                        @enderror

                    </div>


                    {{-- FOTO --}}
                    <div class="inventory-form-group">

                        <label for="foto_bukti">
                            Upload bukti foto kerusakan
                            <span>*</span>
                        </label>

                        <input
                            type="file"
                            name="foto_bukti"
                            id="foto_bukti"
                            accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                            required>

                        <small class="form-help">
                            Format JPG, JPEG, atau PNG.
                            Ukuran maksimal 5 MB.
                        </small>

                        @error('foto_bukti')
                            <small class="form-error">{{ $message }}</small>
                        @enderror

                        <div id="fotoPreview" class="foto-preview"></div>

                    </div>


                    {{-- INFORMASI --}}
                    <div class="inventory-warning">
                        <strong>Perhatian</strong>

                        <p>
                            Setelah pencatatan disimpan, kondisi barang
                            otomatis berubah menjadi Rusak Ringan atau
                            Rusak Berat sesuai pilihan, dan status
                            inventaris berubah menjadi Tidak Tersedia.
                        </p>
                    </div>


                    {{-- TOMBOL --}}
                    <div class="inventory-form-actions">

                        <a href="{{ route('admin.kerusakan.inventaris.index') }}"
                           class="damage-btn secondary">
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="damage-btn inventory-add-btn"
                            {{ $barang->isEmpty() ? 'disabled' : '' }}
                            onclick="return confirm('Simpan pencatatan barang rusak dan ubah status inventaris menjadi tidak tersedia?')">

                            Simpan Data Kerusakan

                        </button>

                    </div>

                </form>

            </section>

        </div>

    </main>

</div>


<script>
    const fotoInput = document.getElementById('foto_bukti');
    const fotoPreview = document.getElementById('fotoPreview');

    fotoInput?.addEventListener('change', function () {
        fotoPreview.innerHTML = '';

        const file = this.files[0];

        if (!file) return;

        if (!['image/jpeg', 'image/png'].includes(file.type)) {
            alert('Gunakan foto JPG, JPEG, atau PNG.');
            this.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran foto maksimal 5 MB.');
            this.value = '';
            return;
        }

        const image = document.createElement('img');
        image.src = URL.createObjectURL(file);
        image.alt = 'Preview foto kerusakan';

        fotoPreview.appendChild(image);
    });
</script>

</body>
</html>
