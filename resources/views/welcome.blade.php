
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SISKO | SMKN 1 KOTA BEKASI</title>
    <meta name="description" content="SISKO, portal informasi dan layanan terpadu.">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="container nav-inner">

            <a href="#beranda" class="brand">
                <div class="brand-icon">S</div>
                <div>
                    <div class="brand-name">SISKO</div>
                    <div class="brand-subtitle">SMKN 1 KOTA BEKASI</div>
                </div>
            </a>

            <button
                class="menu-toggle"
                id="menuToggle"
                aria-label="Buka menu navigasi"
                aria-expanded="false"
                aria-controls="navLinks"
                type="button">
                ☰
            </button>

            <nav class="nav-links" id="navLinks">
                <a href="#beranda">Beranda</a>
                <a href="#layanan">Layanan</a>
                <a href="#tentang">Tentang</a>
                <a href="#informasi">Informasi</a>
                <a href="{{ route('login') }}" class="nav-cta">
                    Login →
                </a>
            </nav>

        </div>
    </header>

    <main>

        
<!-- HERO -->
<section class="hero" id="beranda">
    <div class="container hero-grid">

        <!-- LEFT CONTENT -->
        <div class="hero-copy">

            <div class="eyebrow">
                <span class="eyebrow-dot"></span>
                SISTEM INFORMASI TATA USAHA
            </div>

            <h1>
                Temukan informasi
                sekolah yang
                <span>tercatat dan mudah ditelusuri.</span>
            </h1>

            <p class="hero-description">
                SISKO membantu Tata Usaha mengelola informasi dan
                layanan sekolah secara terstruktur. Mulai dari
                pengelolaan barang, peminjaman, hingga pelaporan
                kerusakan dalam satu portal yang mudah diakses.
            </p>

            <div class="hero-actions">
    @guest
        <!-- Tombol notifikasi informasi (tidak bisa diklik / disabled) -->
        <button type="button" class="btn btn-primary" disabled style="opacity: 0.8; cursor: not-allowed;">
            Silahkan login terlebih dahulu <span>→</span>
        </button>
    
    @else
        @if (auth()->user()->role === 'admin_tu')
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                Lanjut ke dashboard <span>→</span>
            </a>
        @elseif (in_array(auth()->user()->role, ['guru', 'siswa']))
            <a href="{{ route('peminjaman.index') }}" class="btn btn-primary">
                Lanjut ke dashboard <span>→</span>
            </a>
        @endif
    @endguest
</div>

        </div>

        
<!-- RIGHT CONTENT -->
<div class="hero-visual">

    <!-- LOAN PREVIEW CARD -->
    <div class="visual-card">

        <div class="visual-top">

            <div>
                <span class="visual-label">CONTOH DATA PEMINJAMAN</span>
                <h3>PJ-260912-01</h3>
            </div>

            <div class="loan-status">
                <span class="status-pill">DIPINJAM</span>
            </div>

        </div>

        <!-- BORROWED ITEMS -->
        <div class="loan-items">

            <div class="loan-item">
                <span>Speaker</span>
                <span class="quantity">2 Unit</span>
            </div>

            <div class="loan-item">
                <span>Laptop</span>
                <span class="quantity">1 UNIT</span>
            </div>

        </div>

        <!-- LOAN DATES -->
        <div class="loan-dates">

            <div class="date-box">
                <span class="date-label">TANGGAL PINJAM</span>
                <strong>12 September 2026</strong>
            </div>

            <div class="date-box">
                <span class="date-label">RENCANA KEMBALI</span>
                <strong>15 September 2026</strong>
            </div>

        </div>

        <!-- DAMAGE REPORT -->
        <div class="damage-notice">
            <span>1 laporan kerusakan tercatat pada peminjaman ini.</span>
        </div>

    </div>

    <!-- STOCK INFORMATION -->
    <div class="floating-info">

        <div class="floating-info-mark"></div>

        <div>
            <strong>Ketersediaan diperbarui otomatis</strong>

            <p>
                Stok berkurang saat peminjaman disetujui dan
                kembali diperhitungkan saat dikembalikan.
            </p>
        </div>

    </div>

</div>


    </div>
</section>

        <!-- SERVICES -->
        
<!-- SERVICES / THREE MAIN PROCESSES -->
<section class="section services" id="layanan">
    <div class="container">

        <div class="section-heading">

            <span class="section-label">
                TIGA PROSES UTAMA
            </span>

            <h2>
                Fokus pada barang Tata Usaha, dari
                masuk sampai kembali.
            </h2>

            <p>
                Sistem tidak mencakup pengelolaan barang maupun
                pemeliharaan barang milik jurusan, seluruh data
                berada dalam lingkup Tata Usaha.
            </p>

        </div>

        <div class="service-grid">

            <!-- PROCESS 1 -->
            <article class="service-card">

                <div class="service-icon">▤</div>

                <h3>Pengelolaan barang Tata Usaha</h3>

                <p>
                    Admin TU menambah, mengubah, menghapus,
                    dan memantau ketersediaan barang milik
                    Tata Usaha.
                </p>

            </article>

            <!-- PROCESS 2 -->
            <article class="service-card">

                <div class="service-icon">▤</div>

                <h3>Peminjaman &amp; pengembalian</h3>

                <p>
                    Guru dan siswa mengajukan permintaan,
                    diverifikasi Tata Usaha, lalu dicatat
                    saat dikembalikan.
                </p>

            </article>

            <!-- PROCESS 3 -->
            <article class="service-card">

                <div class="service-icon">♧</div>

                <h3>Pelaporan kerusakan</h3>

                <p>
                    Kerusakan dilaporkan berdasarkan
                    peminjaman yang tercatat sehingga
                    mudah ditelusuri.
                </p>

            </article>

        </div>

    </div>
</section>


        <!-- ABOUT -->
        <section class="section" id="tentang">
            <div class="container about-grid">

                <div class="about-visual">
                    <div class="about-orb"></div>
                    <div class="about-symbol">S</div>
                </div>

                <div class="about-copy">

                    <span class="section-label">Tentang SISKO</span>

                    <h2>
                        Mempermudah Akses Informasi
                        dalam Satu Platform
                    </h2>

                    <p>
                        SISKO hadir sebagai konsep portal informasi
                        terpadu untuk membantu pengguna menemukan
                        informasi dan mengenali layanan yang tersedia
                        dengan lebih mudah.
                    </p>

                    <p>
                        Melalui struktur informasi yang jelas dan
                        tampilan yang ramah pengguna, SISKO diharapkan
                        dapat menjadi titik awal untuk mengakses
                        informasi yang dibutuhkan.
                    </p>

                    <div class="about-points">

                        <div class="about-point">
                            <span class="check">✓</span>
                            Informasi disusun secara terstruktur
                        </div>

                        <div class="about-point">
                            <span class="check">✓</span>
                            Navigasi sederhana dan mudah dipahami
                        </div>

                        <div class="about-point">
                            <span class="check">✓</span>
                            Tampilan responsif di berbagai perangkat
                        </div>

                    </div>
                </div>

            </div>
        </section>


<!-- ALUR PEMINJAMAN -->
<section class="section information" id="informasi">
    <div class="container">

        <div class="info-top">
            <div>
                <span class="section-label">
                    ALUR PEMINJAMAN
                </span>

                <h2>Empat langkah yang tercatat rapi.</h2>
            </div>
        </div>

        <div class="process-grid">

            <!-- STEP 1 -->
            <article class="process-item">

                <div class="process-number">
                    <span>1</span>
                    <div class="process-line"></div>
                </div>

                <h3>
                    <span class="process-icon">♧</span>
                    Pilih barang
                </h3>

                <p>
                    Peminjam melihat katalog barang Tata Usaha
                    beserta jumlah yang tersedia.
                </p>

            </article>

            <!-- STEP 2 -->
            <article class="process-item">

                <div class="process-number">
                    <span>2</span>
                    <div class="process-line"></div>
                </div>

                <h3>
                    <span class="process-icon">▤</span>
                    Ajukan permintaan
                </h3>

                <p>
                    Jumlah barang, tanggal peminjaman, dan
                    tujuan penggunaan dicatat dalam sistem.
                </p>

            </article>

            <!-- STEP 3 -->
            <article class="process-item">

                <div class="process-number">
                    <span>3</span>
                    <div class="process-line"></div>
                </div>

                <h3>
                    <span class="process-icon">✓</span>
                    Verifikasi Admin TU
                </h3>

                <p>
                    Permintaan diperiksa; bila disetujui,
                    ketersediaan barang otomatis berkurang.
                </p>

            </article>

            <!-- STEP 4 -->
            <article class="process-item">

                <div class="process-number">
                    <span>4</span>
                </div>

                <h3>
                    <span class="process-icon">↶</span>
                    Pengembalian &amp; laporan
                </h3>

                <p>
                    Barang dikembalikan, kondisinya dicatat,
                    dan kerusakan dapat dilaporkan.
                </p>

            </article>

        </div>

    </div>
</section>


        <!-- CTA -->
        <section class="cta-section" id="kontak">
            <div class="container">

                <div class="cta-box">

                    <h2>Mulai Jelajahi SISKO</h2>

                    <p>
                        Temukan informasi yang Anda perlukan melalui
                        halaman layanan dan informasi utama SISKO.
                    </p>

                    <a href="{{ route('login') }}" class="btn">
                        Login untuk melakukan peminjaman →
                    </a>

                </div>

            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container footer-inner">

            <div class="brand">
                <div class="brand-icon">S</div>

                <div>
                    <div class="brand-name">SISKO</div>
                    <div class="brand-subtitle">
                        SMKN 1 KOTA BEKASI
                    </div>
                </div>
            </div>

            <p>
                &copy; {{ date('Y') }} SISKO.
                Dikembangkan untuk kemudahan akses informasi.
            </p>

            <div class="footer-links">
                <a href="#beranda">Beranda</a>
                <a href="#layanan">Layanan</a>
                <a href="#informasi">Informasi</a>
            </div>

        </div>
    </footer>

    <!-- JAVASCRIPT -->
    <script>
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');

        menuToggle.addEventListener('click', function () {
            const isOpen = navLinks.classList.toggle('open');

            menuToggle.setAttribute(
                'aria-expanded',
                String(isOpen)
            );

            menuToggle.textContent = isOpen ? '✕' : '☰';
        });

        navLinks.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                navLinks.classList.remove('open');

                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.textContent = '☰';
            });
        });
    </script>

</body>
</html>
