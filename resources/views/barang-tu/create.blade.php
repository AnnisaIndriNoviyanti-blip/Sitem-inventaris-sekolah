
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Barang | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/barang.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <style>
        .form-panel {
            max-width: 900px;
            margin: 0 auto;
        }

        .form-panel-header {
            padding: 24px 28px;
            border-bottom: 1px solid #edf0ee;
        }

        .form-panel-header h2 {
            margin: 0 0 8px;
            font-size: 20px;
        }

        .form-panel-header p {
            margin: 0;
            color: #7c8491;
            font-size: 13px;
            line-height: 1.7;
        }

        .inventory-form {
            padding: 28px;
        }

        .form-section-title {
            margin: 0 0 22px;
            color: #28664b;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 9px;
            min-width: 0;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            color: #34443a;
            font-size: 13px;
            font-weight: 600;
        }

        .form-group label span {
            color: #c74a40;
        }

        .form-control {
            width: 100%;
            box-sizing: border-box;
            min-height: 46px;
            padding: 12px 14px;
            border: 1px solid #dfe6e1;
            border-radius: 9px;
            background: #fff;
            color: #34443a;
            font: inherit;
            font-size: 13px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        textarea.form-control {
            min-height: 110px;
            resize: vertical;
            line-height: 1.7;
        }

        .form-control:focus {
            border-color: #347457;
            box-shadow: 0 0 0 3px rgba(52, 116, 87, .10);
        }

        .form-control::placeholder {
            color: #a0a8a2;
        }

        .field-help {
            color: #849087;
            font-size: 11px;
            line-height: 1.6;
        }

        .form-divider {
            margin: 28px 0;
            border: 0;
            border-top: 1px solid #edf0ee;
        }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-top: 30px;
            padding-top: 22px;
            border-top: 1px solid #edf0ee;
        }

        .form-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
        }

        .secondary-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 18px;
            border: 1px solid #dfe6e1;
            border-radius: 9px;
            background: #fff;
            color: #536158;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .secondary-button:hover {
            background: #f7faf8;
        }

        .form-error {
            color: #b43b35;
            font-size: 12px;
        }

        .form-notice {
            margin-bottom: 22px;
            padding: 13px 16px;
            border-radius: 9px;
            background: #fff0ef;
            color: #b43b35;
            font-size: 13px;
        }

        .form-notice ul {
            margin: 8px 0 0;
            padding-left: 20px;
        }

        @media (max-width: 768px) {
            .form-panel-header,
            .inventory-form {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full-width {
                grid-column: auto;
            }

            .form-footer {
                align-items: stretch;
                flex-direction: column;
            }

            .form-actions {
                width: 100%;
            }

            .form-actions > * {
                flex: 1;
            }
        }
    </style>
</head>

<body>
<div class="app-layout">

    <!-- SIDEBAR -->
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

            <a href="{{ route('barang-tu.index') }}" class="nav-link active">
                <span class="nav-icon">▤</span>
                <span>Barang Tata Usaha</span>
            </a>

            <a href="{{ url('/admin/peminjaman') }}" class="nav-link">
                <span class="nav-icon">▧</span>
                <span>Verifikasi Peminjaman</span>
                <span class="nav-badge">{{ $menungguVerifikasi ?? 0 }}</span>
            </a>

            <a href="{{ url('/admin/pengembalian') }}" class="nav-link">
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

    <!-- AREA UTAMA -->
    <main class="main-area">

        <!-- TOPBAR -->
        <header class="topbar">
            <form action="{{ route('barang-tu.index') }}"
                  method="GET"
                  class="search-box">
                <span>⌕</span>
                <input type="search"
                       name="search"
                       placeholder="Cari barang, kode, atau spesifikasi">
            </form>

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

        <!-- KONTEN -->
        <div class="dashboard-content">

            <section class="page-heading">
                <div>
                    <span class="eyebrow">INVENTARIS SEKOLAH</span>
                    <h1>Tambah Barang</h1>
                    <p>
                        Masukkan identitas dan kondisi barang
                        untuk menambahkan inventaris Tata Usaha.
                    </p>
                </div>

                <a href="{{ route('barang-tu.index') }}"
                   class="secondary-button">
                    ← Kembali ke daftar
                </a>
            </section>

            <section class="panel form-panel">

                <div class="form-panel-header">
                    <h2>Formulir Data Barang</h2>
                    <p>
                        Isi data sesuai barang yang tercatat dalam
                        inventaris sekolah. Kolom bertanda
                        <span style="color:#c74a40">*</span>
                        wajib diisi.
                    </p>
                </div>

                <form action="{{ route('barang-tu.store') }}"
                      method="POST"
                      class="inventory-form">
                    @csrf

                    @if ($errors->any())
                        <div class="form-notice">
                            <strong>Data belum dapat disimpan.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h3 class="form-section-title">
                        01 — Identitas Barang
                    </h3>

                    <div class="form-grid">

                        
<div class="form-group">
    <label for="kode_barang">Kode Barang <span>*</span></label>

    <input
        type="text"
        id="kode_barang"
        name="kode_barang"
        class="form-control"
        value="{{ old('kode_barang') }}"
        placeholder="Contoh: BRG 06"
        maxlength="255"
        required
    >

    @error('kode_barang')
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="jenis_barang">Jenis Barang <span>*</span></label>

    <select
        id="jenis_barang"
        name="jenis_barang"
        class="form-control"
        required
    >
        <option value="">Pilih jenis barang</option>

        <option value="paket_proyektor"
            @selected(old('jenis_barang') === 'paket_proyektor')>
            Paket Proyektor (LCD + Kabel HDMI)
        </option>

        <option value="laptop"
            @selected(old('jenis_barang') === 'laptop')>
            Laptop
        </option>

        <option value="speaker"
            @selected(old('jenis_barang') === 'speaker')>
            Speaker
        </option>
    </select>

    @error('jenis_barang')
        <span class="form-error">{{ $message }}</span>
    @enderror

    <small class="field-help">
        Paket proyektor dihitung sebagai satu inventaris,
        tetapi LCD dan kabel dicentang bersama pada buku peminjaman.
    </small>
</div>

                        <div class="form-group full-width">
    <label for="nama_barang">Nama Barang</label>

    <input
        type="text"
        id="nama_barang"
        name="nama_barang"
        class="form-control"
        value="{{ old('nama_barang') }}"
        readonly
        required
    >

    @error('nama_barang')
        <span class="form-error">{{ $message }}</span>
    @enderror
</div>

                        <div class="form-group full-width">
                            <label for="spesifikasi">
                                Spesifikasi Barang
                            </label>

                            <textarea
                                id="spesifikasi"
                                name="spesifikasi"
                                class="form-control"
                                placeholder="Masukkan merek, ukuran, tipe, atau keterangan barang..."
                            >{{ old('spesifikasi') }}</textarea>

                            <small class="field-help">
                                Opsional. Contoh: panjang kabel 5 meter,
                                merek, atau tipe perangkat.
                            </small>

                            @error('spesifikasi')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <hr class="form-divider">

                    <h3 class="form-section-title">
                        02 — Kondisi dan Ketersediaan
                    </h3>

                    <div class="form-grid">

                        <div class="form-group">
                            <label for="kondisi">
                                Kondisi Barang <span>*</span>
                            </label>

                            <select
                                id="kondisi"
                                name="kondisi"
                                class="form-control"
                                required
                            >
                                <option value="">Pilih kondisi barang</option>

                                <option value="baik"
                                    @selected(old('kondisi', 'baik') === 'baik')>
                                    Baik
                                </option>

                                <option value="rusak_ringan"
                                    @selected(old('kondisi') === 'rusak_ringan')>
                                    Rusak Ringan
                                </option>

                                <option value="rusak_berat"
                                    @selected(old('kondisi') === 'rusak_berat')>
                                    Rusak Berat
                                </option>
                            </select>

                            @error('kondisi')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="status">
                                Status Barang <span>*</span>
                            </label>

                            <select
                                id="status"
                                name="status"
                                class="form-control"
                                required
                            >
                                <option value="">Pilih status barang</option>

                                <option value="tersedia"
                                    @selected(old('status', 'tersedia') === 'tersedia')>
                                    Tersedia
                                </option>

                                <option value="dipinjam"
                                    @selected(old('status') === 'dipinjam')>
                                    Dipinjam
                                </option>

                                <option value="tidak_tersedia"
                                    @selected(old('status') === 'tidak_tersedia')>
                                    Tidak Tersedia
                                </option>
                            </select>

                            @error('status')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="form-footer">
                        <small class="field-help">
                            Pastikan data sudah sesuai sebelum disimpan.
                        </small>

                        <div class="form-actions">
                            <a href="{{ route('barang-tu.index') }}"
                               class="secondary-button">
                                Batal
                            </a>

                            <button type="submit" class="primary-button">
                                ✓ Simpan Barang
                            </button>
                        </div>
                    </div>

                </form>
            </section>

            <footer class="dashboard-footer">
                <span>SISKO · Sistem Peminjaman Barang Tata Usaha</span>
                <span>Tambah Inventaris</span>
            </footer>

        </div>
    </main>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const jenis = document.getElementById('jenis_barang');
    const nama = document.getElementById('nama_barang');

    const namaBarang = {
        paket_proyektor: 'Paket Proyektor (LCD + Kabel HDMI)',
        laptop: 'Laptop',
        speaker: 'Speaker'
    };

    function sinkronkanNama() {
        nama.value = namaBarang[jenis.value] || '';
    }

    jenis.addEventListener('change', sinkronkanNama);
    sinkronkanNama();
});
</script>

</body>
</html>
