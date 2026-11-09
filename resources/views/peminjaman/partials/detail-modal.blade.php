<div class="detail-modal-overlay" id="detailModalOverlay">
    <section class="detail-modal" role="dialog"
             aria-modal="true" aria-labelledby="detailModalTitle">

        <header class="detail-modal-header">
            <div>
                <span class="detail-eyebrow">SISKO · DETAIL PEMINJAMAN</span>
                <h2 id="detailModalTitle">Rincian Peminjaman</h2>
                <p>Informasi pengajuan dan status peminjaman barang.</p>
            </div>

            <button type="button" class="detail-close"
                    onclick="closeDetailModal()" aria-label="Tutup">
                &times;
            </button>
        </header>

        <div class="detail-modal-body">
            <div class="detail-status-row">
                <span class="detail-label">Status pengajuan</span>
                <span class="detail-status status-{{ $peminjaman->status }}">
                    {{ ucfirst(str_replace('_', ' ', $peminjaman->status)) }}
                </span>
            </div>

            <div class="detail-section">
                <h3>Informasi Peminjam</h3>

                <div class="detail-grid">
                    <div class="detail-item">
                        <span>Nama peminjam</span>
                        <strong>
                            {{ $peminjaman->nama_peminjam
                                ?? $peminjaman->user?->nama ?? '-' }}
                        </strong>
                    </div>

                    <div class="detail-item">
                        <span>Jenis akun</span>
                        <strong>
                            {{ ucfirst($peminjaman->user?->jenis_akun ?? '-') }}
                        </strong>
                    </div>

                    <div class="detail-item">
                        <span>Tanggal peminjaman</span>
                        <strong>
                            {{ $peminjaman->tanggal_pinjam?->format('d M Y') ?? '-' }}
                        </strong>
                    </div>

                    <div class="detail-item">
                        <span>Rencana pengembalian</span>
                        <strong>
                            {{ $peminjaman->tanggal_rencana_kembali?->format('d M Y') ?? '-' }}
                        </strong>
                    </div>

                    <div class="detail-item">
                        <span>Tanggal dikembalikan</span>
                        <strong>
                            {{ $peminjaman->tanggal_kembali?->format('d M Y') ?? '-' }}
                        </strong>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h3>Barang yang Dipinjam</h3>

                <div class="detail-goods-list">
                    @forelse ($peminjaman->detail as $detail)
                        <div class="detail-goods-item">
                            <div class="detail-goods-icon">▤</div>

                            <div class="detail-goods-info">
                                <strong>
                                    {{ $detail->barang?->nama_barang
                                        ?? 'Barang tidak ditemukan' }}
                                </strong>
                                <span>
                                    Kode:
                                    {{ $detail->barang?->kode_barang ?? '-' }}
                                </span>
                                <span>
                                    Spesifikasi:
                                    {{ $detail->barang?->spesifikasi ?? '-' }}
                                </span>
                            </div>

                            <span class="detail-quantity">
                                {{ $detail->jumlah }} unit
                            </span>

                             @if (
    auth()->user()->role !== 'admin_tu' &&
    $peminjaman->status === 'dipinjam'
)
    <button
        type="button"
        class="detail-button damage-open-button"
        data-detail-id="{{ $detail->id }}"
        data-detail-name="{{ $detail->barang?->nama_barang ?? 'Barang' }}"
        onclick="openDamageModal(this)"
    >
        ♧ Laporkan Kerusakan
    </button>
@endif
                        </div>
                       
                    @empty
                        <p class="detail-muted">Tidak ada rincian barang.</p>
                    @endforelse
                </div>
            </div>

            <div class="detail-section">
                <h3>Keperluan dan Catatan</h3>

                <div class="detail-text-box">
                    <span>Tujuan penggunaan</span>
                    <p>{{ $peminjaman->keperluan ?: '-' }}</p>
                </div>

                <div class="detail-text-box">
                    <span>Catatan tambahan</span>
                    <p>{{ $peminjaman->catatan ?: '-' }}</p>
                </div>
            </div>

            <div class="detail-section">
                <h3>Tanda Tangan Peminjam</h3>

                @if ($peminjaman->ttd_peminjam)
                    <div class="detail-signature">
                        <img
                            src="{{ asset('storage/' . $peminjaman->ttd_peminjam) }}"
                            alt="Tanda tangan peminjam">
                    </div>
                @else
                    <p class="detail-muted">Tanda tangan belum tersedia.</p>
                @endif
            </div>

            <div class="detail-note">
                <span>ⓘ</span>
                <p>
                    Status dan persetujuan peminjaman diproses oleh
                    Admin Tata Usaha. Hubungi TU jika membutuhkan informasi
                    lebih lanjut.
                </p>
            </div>
        </div>

        <footer class="detail-modal-footer">
            <button type="button" class="detail-button"
                    onclick="closeDetailModal()">
                Tutup
            </button>
        </footer>
    </section>
    
<div class="damage-modal-overlay" id="damageModalOverlay"
     aria-hidden="true">
    <section class="damage-modal" role="dialog"
             aria-modal="true"
             aria-labelledby="damageModalTitle">

        <header class="detail-modal-header">
            <div>
                <span class="detail-eyebrow">SISKO · LAPORAN KERUSAKAN</span>
                <h2 id="damageModalTitle">Laporkan Kerusakan Barang</h2>
                <p>Lengkapi informasi kerusakan agar dapat diperiksa Admin TU.</p>
            </div>

            <button type="button" class="detail-close"
                    onclick="closeDamageModal()" aria-label="Tutup">
                &times;
            </button>
        </header>

        <form
            action="{{ route('laporan-kerusakan.store', $peminjaman->id) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="damage-modal-body">

                <div class="damage-info-box">
                    <strong>Informasi peminjaman</strong>
                    <p>
                        Peminjaman #{{ $peminjaman->id }}
                        · {{ $peminjaman->status }}
                    </p>
                    <small>
                        Laporan akan diteruskan kepada Admin Tata Usaha.
                    </small>
                </div>

                <div class="damage-field">
                    <label for="damageDetailId">Barang yang rusak</label>
                    <select name="peminjaman_detail_id"
                            id="damageDetailId" required>
                        <option value="">Pilih barang</option>
                        @foreach ($peminjaman->detail as $detail)
                            <option value="{{ $detail->id }}">
                                {{ $detail->barang?->nama_barang ?? 'Barang tidak ditemukan' }}
                                — {{ $detail->barang?->kode_barang ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="damage-field">
                    <label for="damageType">Jenis laporan</label>
                    <select name="jenis_laporan"
                            id="damageType" required>
                        <option value="saat_diterima">
                            Rusak saat barang diterima
                        </option>
                        <option value="selama_dipinjam">
                            Rusak selama digunakan
                        </option>
                    </select>
                    <small>
                        Pilih sesuai kapan kerusakan diketahui.
                    </small>
                </div>

                <div class="damage-field">
                    <label for="damageDate">Tanggal laporan</label>
                    <input type="date"
                           name="tanggal_laporan"
                           id="damageDate"
                           value="{{ now()->toDateString() }}"
                           max="{{ now()->toDateString() }}"
                           required>
                </div>

                <div class="damage-field">
                    <label for="damageWhatsapp">Nomor WhatsApp</label>
                    <input type="tel"
                           name="nomor_whatsapp"
                           id="damageWhatsapp"
                           maxlength="20"
                           pattern="[0-9+\s()\-]+"
                           placeholder="Contoh: 081234567890"
                           required>
                </div>

                <div class="damage-field">
                    <label for="damageDescription">Keterangan kerusakan</label>
                    <textarea name="keterangan"
                              id="damageDescription"
                              rows="4"
                              maxlength="5000"
                              placeholder="Jelaskan kondisi barang dan kerusakan yang ditemukan..."
                              required></textarea>
                </div>

                <div class="damage-field">
                    <label for="damageEvidence">Foto bukti kerusakan</label>
                    <input type="file"
                           name="bukti_laporan"
                           id="damageEvidence"
                           accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                           required>
                    <small>Format JPG atau PNG, maksimal 5 MB.</small>
                </div>

                <div class="damage-info-box" id="damageReplacementNotice">
                    <strong>Informasi penggantian</strong>
                    <p>
                        Jika barang rusak saat diterima, Admin TU akan
                        memeriksa laporan dan ketersediaan barang pengganti.
                    </p>
                </div>
            </div>

            <footer class="detail-modal-footer">
                <button type="button" class="detail-button"
                        onclick="closeDamageModal()">
                    Batal
                </button>
                <button type="submit" class="damage-submit-button">
                    Kirim Laporan →
                </button>
            </footer>
        </form>
    </section>
</div>
</div>

