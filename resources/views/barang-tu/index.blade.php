<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang Tata Usaha | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/barang.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="app-layout">

    
<!-- SIDEBAR -->
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

            <a href="{{ route('barang-tu.index') }}" class="nav-link active">
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
            <form action="{{ route('barang-tu.index') }}"
                  method="GET" class="search-box">
                <span>⌕</span>
                <input type="search"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari nama, kode, atau spesifikasi barang">
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

        <div class="dashboard-content">

            <section class="page-heading">
                <div>
                    <span class="eyebrow">INVENTARIS SEKOLAH</span>
                    <h1>Barang Tata Usaha</h1>
                    <p>
                        Kelola data inventaris, kondisi, dan
                        ketersediaan barang untuk peminjaman.
                    </p>
                </div>

                <a href="{{ route('barang-tu.create') }}"
                   class="primary-button">
                    <span>＋</span> Tambah barang
                </a>
            </section>

            @if (session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="stats-grid barang-stats">
                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">TOTAL JENIS BARANG</span>
                        <span class="stat-icon green-icon">▤</span>
                    </div>
                    <h2>{{ $totalBarang }}</h2>
                    <p>Jenis inventaris terdaftar</p>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">TERSEDIA</span>
                        <span class="stat-icon green-icon">✓</span>
                    </div>
                    <h2>{{ $barangTersedia }}</h2>
                    <p>Barang berstatus tersedia</p>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">SEDANG DIPINJAM</span>
                        <span class="stat-icon yellow-icon">↗</span>
                    </div>
                    <h2>{{ $barangDipinjam }}</h2>
                    <p>Barang berstatus dipinjam</p>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">TIDAK TERSEDIA</span>
                        <span class="stat-icon orange-icon">!</span>
                    </div>
                    <h2>{{ $barangTidakTersedia }}</h2>
                    <p>Barang tidak tersedia</p>
                </article>
            </section>

            <section class="panel inventory-panel">
                <div class="panel-header inventory-header">
                    <div>
                        <h2>Daftar Inventaris</h2>
                        <p>
                            Data barang Tata Usaha yang tersimpan
                            di dalam sistem.
                        </p>
                    </div>

                    <span class="inventory-count">
                        {{ $barang->total() }} data
                    </span>
                </div>

                <div class="inventory-table-wrap">
                    <table class="inventory-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Spesifikasi</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($barang as $item)
                                <tr>
                                    <td>
                                        {{ $barang->firstItem() + $loop->index }}
                                    </td>
                                    <td>
                                        <span class="item-code">
                                            {{ $item->kode_barang }}
                                        </span>
                                    </td>
                                    <td class="item-name">
                                        {{ $item->nama_barang }}
                                    </td>
                                    <td class="item-spec">
                                        {{ $item->spesifikasi ?: '—' }}
                                    </td>
                                    <td>
                                        <span class="status-pill
                                            {{ $item->kondisi === 'baik'
                                                ? 'pill-good'
                                                : ($item->kondisi === 'rusak_ringan'
                                                    ? 'pill-warning'
                                                    : 'pill-danger') }}">
                                            {{ [
                                                'baik' => 'Baik',
                                                'rusak_ringan' => 'Rusak Ringan',
                                                'rusak_berat' => 'Rusak Berat'
                                            ][$item->kondisi] ?? $item->kondisi }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-pill
                                            {{ $item->status === 'tersedia'
                                                ? 'pill-good'
                                                : ($item->status === 'dipinjam'
                                                    ? 'pill-warning'
                                                    : 'pill-neutral') }}">
                                            {{ [
                                                'tersedia' => 'Tersedia',
                                                'dipinjam' => 'Dipinjam',
                                                'tidak_tersedia' => 'Tidak Tersedia'
                                            ][$item->status] ?? $item->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            
<button type="button"
    class="action-edit"
    onclick="openEditModal(this)"
    data-id="{{ $item->id }}"
    data-kode="{{ $item->kode_barang }}"
    data-jenis="{{ $item->jenis_barang }}"
    data-nama="{{ $item->nama_barang }}"
    data-spesifikasi="{{ $item->spesifikasi }}"
    data-kondisi="{{ $item->kondisi }}"
    data-status="{{ $item->status }}"
    data-update-url="{{ route('barang-tu.update', $item->id) }}">
    Edit
</button>


                                            <form action="{{ route('barang-tu.destroy', $item->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="action-delete">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="inventory-empty">
                                            <span>▤</span>
                                            <strong>Belum ada data barang</strong>
                                            <p>
                                                Tambahkan inventaris pertama
                                                untuk mulai mengelola barang TU.
                                            </p>
                                            <a href="{{ route('barang-tu.create') }}"
                                               class="primary-button">
                                                ＋ Tambah barang
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($barang->hasPages())
                    <div class="inventory-pagination">
                        {{ $barang->links() }}
                    </div>
                @endif
            </section>

            <footer class="dashboard-footer">
                <span>SISKO · Sistem Peminjaman Barang Tata Usaha</span>
                <span>Inventaris Barang</span>
            </footer>
        </div>
    </main>
    
<!-- MODAL EDIT BARANG -->
<div class="modal-overlay" id="editBarangModal"
     onclick="closeEditModalOutside(event)">
    <div class="modal-card" role="dialog"
         aria-modal="true" aria-labelledby="editModalTitle">

        <div class="modal-header">
            <div>
                <span class="modal-eyebrow">INVENTARIS SEKOLAH</span>
                <h2 id="editModalTitle">Edit Barang</h2>
                <p>Perbarui informasi barang Tata Usaha.</p>
            </div>

            <button type="button" class="modal-close"
                    onclick="closeEditModal()"
                    aria-label="Tutup">&times;</button>
        </div>

        <form id="editBarangForm" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-body">

                <div class="form-group">
                    <label for="edit_kode_barang">Kode Barang</label>
                    <input type="text" id="edit_kode_barang"
                           name="kode_barang" required maxlength="255">
                </div>

                <div class="form-group">
                    <label for="edit_jenis_barang">Jenis Barang</label>
                    <select id="edit_jenis_barang" name="jenis_barang" required>
                        <option value="paket_proyektor">Paket Proyektor</option>
                        <option value="laptop">Laptop</option>
                        <option value="speaker">Speaker</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_nama_barang">Nama Barang</label>
                    <input type="text" id="edit_nama_barang"
                           name="nama_barang" required maxlength="255">
                </div>

                <div class="form-group">
                    <label for="edit_spesifikasi">Spesifikasi</label>
                    <textarea id="edit_spesifikasi"
                              name="spesifikasi" rows="3"
                              placeholder="Masukkan spesifikasi barang"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_kondisi">Kondisi</label>
                        <select id="edit_kondisi" name="kondisi" required>
                            <option value="baik">Baik</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_status">Status</label>
                        <select id="edit_status" name="status" required>
                            <option value="tersedia">Tersedia</option>
                            <option value="dipinjam">Dipinjam</option>
                            <option value="tidak_tersedia">Tidak Tersedia</option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="modal-cancel"
                        onclick="closeEditModal()">
                    Batal
                </button>

                <button type="submit" class="modal-save">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

</div>


<script>
    function openEditModal(button) {
        const modal = document.getElementById('editBarangModal');
        const form = document.getElementById('editBarangForm');

        // Isi form berdasarkan barang yang dipilih
        document.getElementById('edit_kode_barang').value =
            button.dataset.kode || '';

        document.getElementById('edit_jenis_barang').value =
            button.dataset.jenis || '';

        document.getElementById('edit_nama_barang').value =
            button.dataset.nama || '';

        document.getElementById('edit_spesifikasi').value =
            button.dataset.spesifikasi || '';

        document.getElementById('edit_kondisi').value =
            button.dataset.kondisi || '';

        document.getElementById('edit_status').value =
            button.dataset.status || '';

        // Arahkan form ke route update barang yang dipilih
        form.action = button.dataset.updateUrl;

        modal.classList.add('show');
        document.body.classList.add('modal-open');
    }

    function closeEditModal() {
        document.getElementById('editBarangModal')
            .classList.remove('show');

        document.body.classList.remove('modal-open');
    }

    function closeEditModalOutside(event) {
        if (event.target.id === 'editBarangModal') {
            closeEditModal();
        }
    }

    // Tutup modal dengan tombol Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeEditModal();
        }
    });
</script>
</body>
</html>