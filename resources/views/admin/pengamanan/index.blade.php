
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengamanan Akun | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        :root {
            --primary: #176b55;
            --primary-dark: #104d3e;
            --primary-light: #e8f5ef;
            --accent: #d9ef8b;
            --text: #1c302a;
            --muted: #718078;
            --white: #ffffff;
            --bg: #f7faf8;
            --border: #e6ece8;
            --shadow: 0 12px 35px rgba(22, 75, 58, 0.08);
        }

        body {
            font-family: Georgia, "Times New Roman", serif;
            color: var(--text);
            background: var(--bg);
            padding: 30px;
            line-height: 1.6;
        }

        .card {
            max-width: 650px;
            margin: 30px auto;
            padding: 30px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        h1 {
            color: var(--primary-dark);
        }

        p {
            color: var(--muted);
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--text);
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 7px;
            box-sizing: border-box;
            font-family: inherit;
            color: var(--text);
            background: var(--white);
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        button {
            padding: 12px 20px;
            border: 0;
            border-radius: 7px;
            background: var(--primary);
            color: var(--white);
            cursor: pointer;
            font-family: inherit;
            font-size: 14px;
            transition: background 0.2s ease;
        }

        button:hover {
            background: var(--primary-dark);
        }

        .success {
            padding: 12px;
            background: var(--primary-light);
            color: var(--primary-dark);
            border-radius: 7px;
            margin-bottom: 18px;
        }

        .error {
            color: #c62828;
            margin-bottom: 18px;
        }

        .reset-section {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid var(--border);
        }

        .reset-section h2 {
            color: #b91c1c;
            font-size: 20px;
        }

        .reset-section p {
            color: var(--muted);
            line-height: 1.6;
        }

        .reset-button {
            background: #b91c1c;
        }

        .reset-button:hover {
            background: #991b1b;
        }

        a {
            color: var(--primary);
            text-decoration: none;
        }

        a:hover {
            color: var(--primary-dark);
        }

        button, a {
            -webkit-tap-highlight-color: transparent;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            .card {
                margin: 15px auto;
                padding: 22px;
            }
        }
    </style>
</head>

<body>

<div class="card">

    <h1>Pengamanan Akun Admin TU</h1>

    <p>
        Atur pertanyaan keamanan untuk membantu
        memulihkan akun jika lupa password.
    </p>

    @if (session('success'))
        <div class="success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- FORM SIMPAN PENGAMANAN --}}
    <form action="{{ route('admin.pengamanan.update') }}"
          method="POST">
        @csrf

        <div class="form-group">
            <label>Pilih pertanyaan keamanan</label>

            <select name="security_question" required>
                <option value="">-- Pilih pertanyaan --</option>

                @foreach ($questions as $question)
                    <option value="{{ $question }}"
                        @selected(
                            old(
                                'security_question',
                                $admin->security_question
                            ) === $question
                        )>
                        {{ $question }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Jawaban keamanan</label>

            <input type="password"
                   name="security_answer"
                   minlength="3"
                   maxlength="100"
                   required
                   autocomplete="new-password">
        </div>

        <div class="form-group">
            <label>Ulangi jawaban keamanan</label>

            <input type="password"
                   name="security_answer_confirmation"
                   required
                   autocomplete="new-password">
        </div>

        <div class="form-group">
            <label>Password Admin TU saat ini</label>

            <input type="password"
                   name="current_password"
                   required
                   autocomplete="current-password">
        </div>

        <button type="submit">
            Simpan Pengamanan
        </button>
    </form>

    {{-- BAGIAN RESET PENGAMANAN --}}
    <div class="reset-section">

        <h2>Reset Pengamanan Akun</h2>

        <p>
            Jika ingin mengganti pengaturan keamanan dari awal,
            gunakan tombol reset di bawah ini. Pertanyaan dan
            jawaban keamanan yang lama akan dihapus.
            Password login Admin TU tidak akan berubah.
        </p>

        <form action="{{ route('admin.pengamanan.reset') }}"
              method="POST"
              onsubmit="return confirm('Yakin ingin mereset pengamanan akun? Pertanyaan dan jawaban keamanan akan dihapus.');">

            @csrf

            <div class="form-group">
                <label>Password Admin TU saat ini</label>

                <input type="password"
                       name="current_password"
                       required
                       autocomplete="current-password">
            </div>

            <button type="submit" class="reset-button">
                Reset Pengamanan Akun
            </button>

        </form>

    </div>

    <p>
        <a href="{{ route('admin.dashboard') }}">
            ← Kembali ke dashboard
        </a>
    </p>

</div>

</body>
</html>
