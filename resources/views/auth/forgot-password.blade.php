
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | SISKO</title>

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
            display: grid;
            place-items: center;
            min-height: 100vh;
            margin: 0;
            line-height: 1.6;
        }

        .card {
            width: min(420px, 90%);
            background: var(--white);
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            box-sizing: border-box;
        }

        h2 {
            color: var(--primary-dark);
        }

        p {
            color: var(--muted);
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0 18px;
            border: 1px solid var(--border);
            border-radius: 7px;
            box-sizing: border-box;
            font-family: inherit;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        button {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: var(--white);
            border: 0;
            border-radius: 7px;
            cursor: pointer;
            font-family: inherit;
            font-size: 15px;
            transition: background 0.2s ease;
        }

        button:hover {
            background: var(--primary-dark);
        }

        .error {
            color: #c62828;
        }

        a {
            color: var(--primary);
            text-decoration: none;
        }

        a:hover {
            color: var(--primary-dark);
        }

        button,
        a {
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>

<body>

<div class="card">

    <h2>Lupa Password Admin TU?</h2>

    <p>
        Masukkan username Admin TU untuk melanjutkan
        pemulihan akun.
    </p>

    @if ($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.question') }}"
          method="POST">
        @csrf

        <label>Username Admin TU</label>

        <input type="text"
               name="username"
               value="{{ old('username') }}"
               required
               autocomplete="username"
               placeholder="Masukkan username">

        <button type="submit">
            Lanjutkan
        </button>
    </form>

    <p>
        <a href="{{ route('login') }}">← Kembali ke login</a>
    </p>

</div>

</body>
</html>
