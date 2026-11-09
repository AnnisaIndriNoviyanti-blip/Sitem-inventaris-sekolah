
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peminjaman Saya | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/peminjaman.css') }}">
    <link rel="stylesheet" href="{{ asset('css/laporan-kerusakan-modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kelas/dashboard.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    
</head>
<body>
<div class="student-app-layout">
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
            class="kelas-nav-link active">
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

    <div class="student-main-area">
        <header class="student-topbar">
            <div class="student-topbar-title">
                <span class="eyebrow">SISKO · PEMINJAMAN BARANG</span>
                <strong>Peminjaman Saya</strong>
            </div>
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

<main class="page">
    <section class="heading">
        <div>
            <span class="eyebrow">RIWAYAT PEMINJAMAN</span>
            <h1>Peminjaman Saya</h1>
            <p class="muted">
                Pantau status pengajuan, catat pengembalian barang,
                dan laporkan kerusakan bila ditemukan.
            </p>
        </div>

        @if(in_array(auth()->user()->role, ['guru', 'siswa'], true))
            <button class="button button-gold" type="button" id="openModal">
                ＋ Ajukan peminjaman
            </button>
        @endif
    </section>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="summary">
        <span>{{ $peminjaman->total() }} pengajuan tercatat</span>
        <span>Inventaris Tata Usaha</span>
    </div>

    <section class="panel">
        <div class="panel-head">
            <h2>Semua pengajuan saya</h2>
            <p class="muted">
                Klik Detail untuk melihat rincian barang, tanggal, dan status peminjaman.
            </p>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>TANGGAL</th>
                        <th>NAMA PEMINJAM</th>
                        <th>BARANG</th>
                        <th>RENCANA KEMBALI</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjaman as $item)
                        <tr>
                            <td>
                                {{ $item->tanggal_pinjam?->format('d M Y') ?? '-' }}
                            </td>
                            <td>
                                {{ $item->nama_peminjam ?? $item->user?->nama ?? '-' }}
                                @if($item->keperluan)
                                    <div class="muted" style="font-size:11px;margin-top:4px">
                                        {{ \Illuminate\Support\Str::limit($item->keperluan, 55) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @forelse($item->detail as $detail)
                                    <div style="margin-bottom:5px">
                                        {{ $detail->barang?->nama_barang ?? 'Barang tidak ditemukan' }}
                                        @if($detail->barang?->kode_barang)
                                            <small class="muted">({{ $detail->barang->kode_barang }})</small>
                                        @endif
                                    </div>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>
                                {{ $item->tanggal_rencana_kembali?->format('d M Y')
                                    ?? $item->tanggal_kembali?->format('d M Y')
                                    ?? '-' }}
                            </td>
                            
<td>
    <span class="status status-{{ $item->status }}">
        {{ $item->status === 'dikembalikan'
            ? 'Sudah Dikembalikan'
            : ucfirst(str_replace('_', ' ', $item->status)) }}
    </span>
</td>

                           
<td>
    <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">

        <button
            type="button"
            class="detail-link"
            data-detail-url="{{ route('peminjaman.show', $item->id) }}"
            onclick="openDetailModal(this.dataset.detailUrl)">
            Detail →
        </button>

        @if ($item->status === 'dipinjam')
            <button
                type="button"
                class="detail-link damage-report-trigger"
                data-detail-url="{{ route('peminjaman.show', $item->id) }}"
                onclick="openDetailModal(this.dataset.detailUrl, true)">
                ♧ Laporkan Kerusakan
            </button>
        @endif

    </div>
</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">
                                Belum ada pengajuan peminjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $peminjaman->links() }}
        </div>
    </section>
</main>
    </div>
</div>

@if(in_array(auth()->user()->role, ['guru', 'siswa'], true))
<div class="modal-overlay" id="loanModal" aria-hidden="true">
    <section class="modal" role="dialog" aria-modal="true"
             aria-labelledby="modalTitle">

        <header class="modal-header">
            <div>
                <span class="eyebrow">FORMULIR PEMINJAMAN</span>
                <h2 id="modalTitle">Ajukan Peminjaman Barang</h2>
                <p>Pilih barang yang diperlukan, tentukan tanggal, lalu lengkapi tujuan penggunaan.</p>
            </div>
            <button class="close-modal" type="button" id="closeModal"
                    aria-label="Tutup modal">&times;</button>
        </header>

        <form action="{{ route('peminjaman.store') }}"
              method="POST" enctype="multipart/form-data" id="loanForm">
            @csrf

            <div class="modal-body">
                <div class="form-error" id="formError"></div>

                <div class="form-layout">
                    <section>
                        <div class="section-title">
                            <h3>Barang tersedia</h3>
                            <span class="count-pill">{{ $barang->count() }} unit</span>
                        </div>

                        <input type="search" class="search-input"
                               id="barangSearch"
                               placeholder="Cari nama atau kode barang"
                               autocomplete="off">

                        <div class="goods-list" id="goodsList">
                            @forelse($barang as $item)
                                <label class="goods-item"
                                       data-search="{{ strtolower($item->nama_barang.' '.$item->kode_barang.' '.($item->spesifikasi ?? '')) }}">
                                    <input class="goods-check"
                                           type="checkbox"
                                           name="barang[{{ $item->id }}][barang_tu_id]"
                                           value="{{ $item->id }}"
                                           data-id="{{ $item->id }}"
                                           data-name="{{ $item->nama_barang }}"
                                           data-code="{{ $item->kode_barang }}"
                                           data-spec="{{ $item->spesifikasi ?? '' }}">

                                    <span class="goods-symbol">▤</span>

                                    <span class="goods-info">
                                        <strong>{{ $item->nama_barang }}</strong>
                                        <small>Kode: {{ $item->kode_barang }}</small>
                                        @if($item->spesifikasi)
                                            <small>{{ $item->spesifikasi }}</small>
                                        @endif
                                    </span>
                                </label>
                                
                            @empty
                                <div class="goods-empty">
                                    Belum ada barang tersedia dengan kondisi baik.
                                </div>
                            @endforelse
                        </div>

                        <p class="muted" style="font-size:11px">
                            Pilih setiap unit berdasarkan kode inventarisnya.
                            Paket tas proyektor akan diaktifkan setelah data paket tersedia di database.
                        </p>
                    </section>

                    <section>
                        <div class="selected-panel">
                            <h3>Rincian permintaan</h3>
                            <div id="selectedItems">
                                <div class="selected-empty">
                                    Belum ada barang dipilih.<br>
                                    Klik barang di sebelah kiri untuk menambahkannya.
                                </div>
                            </div>
                        </div>

                        <div class="field-group">
                            <label for="tanggal_pinjam">Tanggal peminjaman</label>
                            <input class="field" type="date"
                                   name="tanggal_pinjam" id="tanggal_pinjam"
                                   min="{{ now()->toDateString() }}"
                                   value="{{ old('tanggal_pinjam', now()->toDateString()) }}"
                                   required>
                        </div>

                        <div class="field-group">
                            <label for="tanggal_rencana_kembali">Rencana pengembalian</label>
                            <input class="field" type="date"
                                   name="tanggal_rencana_kembali"
                                   id="tanggal_rencana_kembali"
                                   min="{{ old('tanggal_pinjam', now()->toDateString()) }}"
                                   value="{{ old('tanggal_rencana_kembali') }}"
                                   required>
                        </div>

                        
@if(in_array(auth()->user()->jenis_akun, ['kelas', 'organisasi'], true))
    <div class="field-group">
        <label for="nama_peminjam">
            {{ auth()->user()->jenis_akun === 'kelas'
                ? 'Nama guru penanggung jawab'
                : 'Nama siswa peminjam (ketua/penanggung jawab)' }}
        </label>

        <input class="field"
               type="text"
               name="nama_peminjam"
               id="nama_peminjam"
               value="{{ old('nama_peminjam') }}"
               maxlength="255"
               required
               placeholder="{{ auth()->user()->jenis_akun === 'kelas'
                    ? 'Masukkan nama guru'
                    : 'Masukkan nama siswa yang meminjam' }}">
    </div>
@endif


                        <div class="field-group">
                            <label for="keperluan">Tujuan penggunaan</label>
                            <textarea class="field" name="keperluan"
                                      id="keperluan" maxlength="1000"
                                      required
                                      placeholder="Contoh: Presentasi kegiatan sekolah">{{ old('keperluan') }}</textarea>
                        </div>

                        <div class="field-group">
                            <label for="catatan">Catatan tambahan (opsional)</label>
                            <textarea class="field" name="catatan"
                                      id="catatan" maxlength="2000"
                                      placeholder="Tambahkan informasi bila diperlukan">{{ old('catatan') }}</textarea>
                        </div>

                        
<div class="field-group">
    <label for="signatureCanvas">Tanda tangan peminjam</label>

    <div class="signature-box">
        <div class="signature-canvas-wrapper">
            <canvas
                id="signatureCanvas"
                width="900"
                height="260"
                aria-label="Area untuk menggambar tanda tangan">
            </canvas>
        </div>

        <input
            type="hidden"
            name="ttd_peminjam_data"
            id="ttd_peminjam_data"
            value="">

        <div class="signature-actions">
            <small>Gambarkan tanda tanganmu di area di atas.</small>

            <button
                type="button"
                id="clearSignature"
                class="button button-outline">
                Hapus Tanda Tangan
            </button>
        </div>
    </div>
</div>


                        <div class="notice">
                            <span>ⓘ</span>
                            <span>Pengajuan akan menunggu persetujuan Admin TU. Barang belum dianggap dipinjam sebelum disetujui dan diserahkan.</span>
                        </div>
                    </section>
                </div>
            </div>

            <footer class="modal-footer">
                <button class="button button-outline" type="button"
                        id="cancelModal">Batal</button>
                <button class="button button-gold" type="submit"
                        id="submitLoan">Kirim pengajuan →</button>
            </footer>
        </form>
    </section>
</div>
@endif

<script>
    const modal = document.getElementById('loanModal');
    const openButton = document.getElementById('openModal');
    const closeButton = document.getElementById('closeModal');
    const cancelButton = document.getElementById('cancelModal');
    const searchInput = document.getElementById('barangSearch');
    const selectedContainer = document.getElementById('selectedItems');
    const formError = document.getElementById('formError');
    const loanForm = document.getElementById('loanForm');
    const signatureCanvas = document.getElementById('signatureCanvas');
    const signatureInput = document.getElementById('ttd_peminjam_data');
    const clearSignatureButton = document.getElementById('clearSignature');
    
    let signatureContext = null;
    let isDrawingSignature = false;
    let hasSignature = false;
    let signatureStrokes = [];
    let activeSignatureStroke = null;
    
    if (signatureCanvas) {
    signatureContext = signatureCanvas.getContext('2d');

    signatureContext.strokeStyle = '#172033';
    signatureContext.lineWidth = 3;
    signatureContext.lineCap = 'round';
    signatureContext.lineJoin = 'round';

    function getSignaturePosition(event) {
        const rect = signatureCanvas.getBoundingClientRect();

        return {
            x: (event.clientX - rect.left) *
                (signatureCanvas.width / rect.width),
            y: (event.clientY - rect.top) *
                (signatureCanvas.height / rect.height)
        };
    }

    signatureCanvas.addEventListener('pointerdown', function (event) {
        event.preventDefault();

        isDrawingSignature = true;
        signatureCanvas.setPointerCapture(event.pointerId);

        const position = getSignaturePosition(event);
        activeSignatureStroke = [position];
        signatureStrokes.push(activeSignatureStroke);

        signatureContext.beginPath();
        signatureContext.moveTo(position.x, position.y);
        signatureContext.lineTo(position.x + 0.1, position.y + 0.1);
        signatureContext.stroke();

        hasSignature = true;
    });

    signatureCanvas.addEventListener('pointermove', function (event) {
        if (!isDrawingSignature) return;

        event.preventDefault();

        const position = getSignaturePosition(event);
        activeSignatureStroke.push(position);

        signatureContext.lineTo(position.x, position.y);
        signatureContext.stroke();
    });

    function stopDrawingSignature() {
        if (!isDrawingSignature) return;

        isDrawingSignature = false;
        activeSignatureStroke = null;
        signatureContext.beginPath();
    }

    signatureCanvas.addEventListener(
        'pointerup',
        stopDrawingSignature
    );

    signatureCanvas.addEventListener(
        'pointercancel',
        stopDrawingSignature
    );

    signatureCanvas.addEventListener(
        'lostpointercapture',
        stopDrawingSignature
    );

    clearSignatureButton?.addEventListener('click', function () {
        signatureContext.clearRect(
            0,
            0,
            signatureCanvas.width,
            signatureCanvas.height
        );

        signatureContext.beginPath();
        hasSignature = false;
        signatureStrokes = [];
        activeSignatureStroke = null;
        signatureInput.value = '';
    });
}


    function openModal() {
        if (!modal) return;
        modal.classList.add('open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

    openButton?.addEventListener('click', openModal);
    closeButton?.addEventListener('click', closeModal);
    cancelButton?.addEventListener('click', closeModal);

    modal?.addEventListener('click', function (event) {
        if (event.target === modal) closeModal();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal?.classList.contains('open')) {
            closeModal();
        }
    });

    function updateSelectedItems() {
        const checks = [
            ...document.querySelectorAll('.goods-check:checked')
        ];

        document.querySelectorAll('.goods-item').forEach(item => {
            const check = item.querySelector('.goods-check');
            item.classList.toggle('selected', !!check?.checked);
        });

        if (!checks.length) {
            selectedContainer.innerHTML =
                '<div class="selected-empty">Belum ada barang dipilih.<br>Klik barang di sebelah kiri untuk menambahkannya.</div>';
            return;
        }

        selectedContainer.innerHTML = checks.map(check => {
            const id = check.dataset.id;
            const name = escapeHtml(check.dataset.name);
            const code = escapeHtml(check.dataset.code);

            return `
                <div class="selected-row">
                    <div>
                        <strong>${name}</strong>
                        <small>Kode: ${code}</small>
                    </div>
                    <button type="button" class="remove-item"
                            data-remove="${id}" aria-label="Hapus barang">&times;</button>
                </div>`;
        }).join('');

        selectedContainer.querySelectorAll('[data-remove]').forEach(button => {
            button.addEventListener('click', function () {
                const check = document.querySelector(
                    '.goods-check[data-id="' + this.dataset.remove + '"]'
                );
                if (check) {
                    check.checked = false;
                    updateSelectedItems();
                }
            });
        });
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, char => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[char]));
    }

    document.querySelectorAll('.goods-check').forEach(check => {
        check.addEventListener('change', updateSelectedItems);
    });

    searchInput?.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();

        document.querySelectorAll('.goods-item').forEach(item => {
            item.style.display = item.dataset.search.includes(keyword)
                ? 'flex'
                : 'none';
        });
    });

    const startDate = document.getElementById('tanggal_pinjam');
    const endDate = document.getElementById('tanggal_rencana_kembali');

    startDate?.addEventListener('change', function () {
        if (endDate) {
            endDate.min = this.value;

            if (endDate.value && endDate.value < this.value) {
                endDate.value = this.value;
            }
        }
    });

    
loanForm?.addEventListener('submit', function (event) {
    const selected = document.querySelectorAll('.goods-check:checked');

    // Hapus jumlah tersembunyi yang mungkin dibuat sebelumnya.
    loanForm.querySelectorAll('.jumlah-dinamis').forEach(input => {
        input.remove();
    });

    if (!selected.length) {
        event.preventDefault();
        formError.textContent =
            'Pilih minimal satu barang sebelum mengirim pengajuan.';
        formError.style.display = 'block';
        return;
    }

    if (
        startDate.value &&
        endDate.value &&
        endDate.value < startDate.value
    ) {
        event.preventDefault();
        formError.textContent =
            'Rencana pengembalian tidak boleh sebelum tanggal peminjaman.';
        formError.style.display = 'block';
        return;
    }
    
    if (!hasSignature) {
        event.preventDefault();

        formError.textContent =
            'Silakan gambar tanda tangan terlebih dahulu.';
        formError.style.display = 'block';
        return;
    }

    // Kirim koordinat goresan sebagai JSON, bukan gambar PNG.
    signatureInput.value = JSON.stringify({
        width: signatureCanvas.width,
        height: signatureCanvas.height,
        strokes: signatureStrokes
    });


    // Kirim jumlah hanya untuk barang yang benar-benar dipilih.
    selected.forEach(check => {
        const jumlah = document.createElement('input');

        jumlah.type = 'hidden';
        jumlah.name =
            `barang[${check.dataset.id}][jumlah]`;
        jumlah.value = '1';
        jumlah.classList.add('jumlah-dinamis');

        loanForm.appendChild(jumlah);
    });

    formError.style.display = 'none';
});


    // Buka kembali modal jika validasi server gagal.
    @if($errors->any())
        openModal();
    @endif
</script>

<link rel="stylesheet" href="{{ asset('css/detail-peminjaman.css') }}">

<div id="detailModalContainer"></div>

<script>

async function openDetailModal(url, openDamageAfterLoad = false) {
    const container = document.getElementById('detailModalContainer');

    if (!container) return;

    container.innerHTML =
        '<p style="padding:20px">Memuat detail...</p>';

    try {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error('Gagal memuat detail peminjaman.');
        }

        container.innerHTML = await response.text();

        document.body.classList.add('detail-modal-open');

        const overlay = document.getElementById('detailModalOverlay');

        overlay?.addEventListener('click', function (event) {
            if (event.target === overlay) {
                closeDetailModal();
            }
        });

        document.removeEventListener(
            'keydown',
            handleDetailEscape
        );

        document.addEventListener(
            'keydown',
            handleDetailEscape
        );

        // Jika tombol Laporkan Kerusakan yang diklik,
        // buka popup form laporan setelah detail dimuat.
        if (openDamageAfterLoad) {
            const damageOverlay =
                document.getElementById('damageModalOverlay');

            if (damageOverlay) {
                damageOverlay.classList.add('open');
                damageOverlay.setAttribute('aria-hidden', 'false');

                document.body.classList.add('damage-modal-open');
            } else {
                alert(
                    'Popup laporan kerusakan belum ditemukan. ' +
                    'Pastikan form laporan tersedia di detail-modal.blade.php.'
                );
            }
        }

    } catch (error) {
        container.innerHTML = '';
        alert(error.message);
    }
}


function openDamageModal(button) {
    const overlay = document.getElementById('damageModalOverlay');
    const select = document.getElementById('damageDetailId');

    if (!overlay || !select) {
        alert('Form laporan kerusakan tidak ditemukan.');
        return;
    }

    select.value = button.dataset.detailId || '';

    overlay.classList.add('open');
    overlay.setAttribute('aria-hidden', 'false');

    document.body.classList.add('damage-modal-open');
}

function closeDamageModal() {
    const overlay = document.getElementById('damageModalOverlay');

    if (!overlay) return;

    overlay.classList.remove('open');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('damage-modal-open');
}

document.addEventListener('click', function (event) {
    const overlay = document.getElementById('damageModalOverlay');

    if (overlay && event.target === overlay) {
        closeDamageModal();
    }
});

document.addEventListener('keydown', function (event) {
    if (
        event.key === 'Escape' &&
        document.getElementById('damageModalOverlay')?.classList.contains('open')
    ) {
        closeDamageModal();
    }
});

document.addEventListener('change', function (event) {
    if (event.target.id !== 'damageType') return;

    const notice = document.getElementById('damageReplacementNotice');

    if (!notice) return;

    if (event.target.value === 'saat_diterima') {
        notice.innerHTML = `
            <strong>Informasi penggantian</strong>
            <p>Admin TU akan memeriksa laporan dan ketersediaan barang
            pengganti sebelum menyetujui penggantian.</p>
        `;
    } else {
        notice.innerHTML = `
            <strong>Informasi penanganan</strong>
            <p>Laporan akan diperiksa Admin TU. Tidak ada tombol
            pengajuan pengembalian tambahan.</p>
        `;
    }
});


function closeDetailModal() {
    document.getElementById('detailModalContainer').innerHTML = '';
    document.body.classList.remove('detail-modal-open');
    document.removeEventListener('keydown', handleDetailEscape);
}

function handleDetailEscape(event) {
    if (event.key === 'Escape') {
        closeDetailModal();
    }
}
</script>


</body>
</html>
