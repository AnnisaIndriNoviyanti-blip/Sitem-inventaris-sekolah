
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar Akun | SISKO</title>
    <meta name="description"
          content="Pendaftaran akun SISKO untuk peminjaman barang Tata Usaha.">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/daftar.css') }}">
</head>
<body class="register-page">

    <main class="register-layout">

        <!-- BAGIAN FORM -->
        <section class="register-form-side">
            <div class="register-form-wrap">

                <a href="{{ url('/') }}" class="register-brand">
                    <span class="register-brand-icon">S</span>
                    <span>
                        <strong>SISKO</strong>
                        <small>SMKN 1 KOTA BEKASI</small>
                    </span>
                </a>

                <div class="register-heading">
                    <span class="register-eyebrow">
                        PENDAFTARAN AKUN
                    </span>
                    <h1>Buat akun peminjaman</h1>
                    <p>
                        Akun ini digunakan untuk mengajukan peminjaman
                        barang Tata Usaha dan melaporkan kerusakan
                        barang yang Anda pinjam.
                    </p>
                </div>

                <form id="registerForm" action="#" method="POST">
                    @csrf

                    <div class="form-group">
                        <label>Daftar sebagai</label>

                        <div class="role-options">
                            <label class="role-card active" for="roleSiswa">
                                <input type="radio" name="role"
                                       id="roleSiswa" value="siswa"
                                       checked>

                                <span class="role-icon">♟</span>
                                <strong>Siswa</strong>
                                <small>
                                    Peminjaman untuk kegiatan kelas
                                    dan kegiatan sekolah.
                                </small>
                            </label>

                            <label class="role-card" for="roleGuru">
                                <input type="radio" name="role"
                                       id="roleGuru" value="guru">

                                <span class="role-icon">♙</span>
                                <strong>Guru</strong>
                                <small>
                                    Peminjaman untuk pembelajaran
                                    dan kegiatan dinas sekolah.
                                </small>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nama">Nama lengkap</label>
                        <input type="text" id="nama" name="nama"
                               placeholder="Contoh: Nadia Prameswari"
                               autocomplete="name" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="identitas" id="identitasLabel">
                                NIS
                            </label>
                            <input type="text" id="identitas"
                                   name="identitas"
                                   placeholder="Masukkan NIS"
                                   required>
                        </div>

                        <div class="form-group" id="kelasGroup">
                            <label for="kelas">Kelas</label>
                            <input type="text" id="kelas" name="kelas"
                                   placeholder="Contoh: XI IPA 3"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email sekolah</label>
                        <input type="email" id="email" name="email"
                               placeholder="nama@sekolah.sch.id"
                               autocomplete="email" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Kata sandi</label>
                            <input type="password" id="password"
                                   name="password"
                                   placeholder="Minimal 6 karakter"
                                   minlength="6"
                                   autocomplete="new-password"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="konfirmasi">
                                Konfirmasi kata sandi
                            </label>
                            <input type="password" id="konfirmasi"
                                   name="konfirmasi"
                                   placeholder="Ulangi kata sandi"
                                   minlength="6"
                                   autocomplete="new-password"
                                   required>
                        </div>
                    </div>

                    <div class="register-notice">
                        <span>ⓘ</span>
                        <p>
                            Akun peminjaman hanya untuk guru dan siswa.
                            Akun Admin Tata Usaha dikelola langsung
                            oleh pihak sekolah.
                        </p>
                    </div>

                    <button type="submit" class="register-submit">
                        Daftar dan masuk <span>→</span>
                    </button>

                    <p class="register-login">
                        Sudah punya akun?
                        <a href="{{ url('/masuk') }}">Masuk di sini</a>
                    </p>
                </form>
            </div>
        </section>

        <!-- PANEL INFORMASI -->
        <aside class="register-info-side">
            <div class="info-brand">
                <span class="info-brand-icon">▤</span>
                <div>
                    <h2>SISKO</h2>
                    <p>SISTEM PEMINJAMAN BARANG TATA USAHA</p>
                </div>
            </div>

            <div class="info-content">
                <span class="info-kicker">LAYANAN SEKOLAH TERPADU</span>

                <h2>
                    Satu akun sekolah untuk seluruh proses peminjaman
                    barang Tata Usaha.
                </h2>

                <p class="info-description">
                    Daftarkan diri Anda agar pengajuan peminjaman,
                    pengembalian, dan laporan kerusakan tercatat
                    pada satu sistem yang sama.
                </p>

                <div class="info-features">
                    <div class="info-feature">
                        <span class="feature-icon">♙</span>
                        <div>
                            <strong>Khusus guru dan siswa</strong>
                            <p>
                                Akun peminjaman hanya untuk warga
                                sekolah yang meminjam barang TU.
                            </p>
                        </div>
                    </div>

                    <div class="info-feature">
                        <span class="feature-icon">♧</span>
                        <div>
                            <strong>Verifikasi Tata Usaha</strong>
                            <p>
                                Setiap pengajuan diperiksa Admin TU
                                sebelum barang keluar.
                            </p>
                        </div>
                    </div>

                    <div class="info-feature">
                        <span class="feature-icon">◇</span>
                        <div>
                            <strong>Riwayat terjaga</strong>
                            <p>
                                Peminjaman, pengembalian, dan laporan
                                kerusakan tercatat rapi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-footer">
                SISKO · SMKN 1 KOTA BEKASI
            </div>
        </aside>

    </main>

    <script>
        const roleInputs = document.querySelectorAll(
            'input[name="role"]'
        );
        const roleCards = document.querySelectorAll('.role-card');
        const identitasLabel = document.getElementById('identitasLabel');
        const identitasInput = document.getElementById('identitas');
        const kelasGroup = document.getElementById('kelasGroup');
        const kelasInput = document.getElementById('kelas');
        const registerForm = document.getElementById('registerForm');

        function updateRole() {
            const role = document.querySelector(
                'input[name="role"]:checked'
            ).value;

            roleCards.forEach(card => {
                card.classList.toggle(
                    'active',
                    card.querySelector('input').checked
                );
            });

            if (role === 'guru') {
                identitasLabel.textContent = 'NIP';
                identitasInput.placeholder = 'Masukkan NIP';
                kelasGroup.hidden = true;
                kelasInput.required = false;
            } else {
                identitasLabel.textContent = 'NIS';
                identitasInput.placeholder = 'Masukkan NIS';
                kelasGroup.hidden = false;
                kelasInput.required = true;
            }
        }

        roleInputs.forEach(input => {
            input.addEventListener('change', updateRole);
        });

        registerForm.addEventListener('submit', function (event) {
            const password = document.getElementById('password').value;
            const konfirmasi = document.getElementById('konfirmasi').value;

            if (password !== konfirmasi) {
                event.preventDefault();
                alert('Konfirmasi kata sandi tidak sama.');
                document.getElementById('konfirmasi').focus();
                return;
            }

            // Form belum dihubungkan ke proses penyimpanan akun.
            event.preventDefault();
        });

        updateRole();
    </script>

</body>
</html>
