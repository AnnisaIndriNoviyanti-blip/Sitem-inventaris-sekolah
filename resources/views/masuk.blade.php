<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/daftar.css') }}">
</head>
<body class="register-page">

<main class="register-layout">

    <section class="register-form-side">
        <div class="register-form-wrap">

            <a href="{{ route('home') }}" class="register-brand">
                <span class="register-brand-icon">S</span>
                <span>
                    <strong>SISKO</strong>
                    <small>SMKN 1 KOTA BEKASI</small>
                </span>
            </a>

            <div class="register-heading">
                <span class="register-eyebrow">SELAMAT DATANG</span>
                <h1>Masuk ke SISKO</h1>
                <p>
                    Gunakan akun kelas atau organisasi
                    yang telah dibuat oleh Admin TU.
                </p>
            </div>

            <form action="{{ route('login.store') }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="register-notice" role="alert">
                        <p>{{ $errors->first() }}</p>
                    </div>
                @endif

                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Masukkan username"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
    <label for="password">Kata sandi</label>

    <div class="password-wrapper">
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Masukkan kata sandi"
            autocomplete="current-password"
            required
        >

        <button
            type="button"
            class="toggle-password"
            id="togglePassword"
            aria-label="Tampilkan kata sandi"
        >
            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg"
                width="20" height="20" viewBox="0 0 24 24"
                fill="none" stroke="currentColor"
                stroke-width="1.8" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
        </button>
    </div>
</div>

                <button type="submit" class="register-submit">
                    Masuk <span>→</span>
                </button>

                <p class="register-login">
                    Akun dibuat oleh Admin TU.
                </p>
                <p class="register-login">
    <a href="{{ route('password.request') }}">
        Lupa Password Admin TU?
    </a>
</p>

                <p class="register-login">
                    <a href="{{ route('home') }}">
                        ← Kembali ke beranda
                    </a>
                </p>

            </form>
        </div>
    </section>

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

            <h2>Peminjaman barang sekolah jadi lebih mudah.</h2>

            <p class="info-description">
                Ajukan peminjaman, pantau persetujuan,
                dan lihat riwayat penggunaan barang melalui
                satu sistem.
            </p>
        </div>

        <div class="info-footer">
            SISKO · SMKN 1 KOTA BEKASI
        </div>
    </aside>

</main>

<script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', function () {
        const isPassword = passwordInput.type === 'password';

        passwordInput.type = isPassword ? 'text' : 'password';

        if (isPassword) {
            // Ikon mata tertutup
            eyeIcon.innerHTML = `
                <path d="m3 3 18 18"/>
                <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                <path d="M9.9 5.2A11 11 0 0 1 12 5c6.5 0 10 7 10 7a13 13 0 0 1-3 3.8"/>
                <path d="M6.6 6.6C3.7 8.5 2 12 2 12s3.5 7 10 7c1.1 0 2.1-.2 3-.5"/>
            `;
            togglePassword.setAttribute('aria-label', 'Sembunyikan kata sandi');
        } else {
            // Ikon mata terbuka
            eyeIcon.innerHTML = `
                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                <circle cx="12" cy="12" r="3"/>
            `;
            togglePassword.setAttribute('aria-label', 'Tampilkan kata sandi');
        }
    });
</script>
</body>
</html>