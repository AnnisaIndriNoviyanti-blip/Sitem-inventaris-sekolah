<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pertanyaan Keamanan | SISKO</title>
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
            font-family: Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            display: grid;
            place-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .card {
            width: min(420px, 90%);
            padding: 30px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        .form-group {
            position: relative;
            margin-bottom: 18px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
            color: var(--text);
        }
        input {
            width: 100%;
            padding: 12px 45px 12px 12px; /* Ruang di kanan untuk ikon mata */
            border: 1px solid var(--border);
            border-radius: 7px;
            box-sizing: border-box;
            font-size: 14px;
            background: var(--bg);
            color: var(--text);
        }
        input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
        }
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 38px; /* Sesuaikan posisi vertikal dengan input */
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            color: var(--muted);
        }
        .toggle-password svg {
            width: 20px;
            height: 20px;
        }
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: var(--white);
            border: 0;
            border-radius: 7px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
            transition: background 0.2s;
        }
        button[type="submit"]:hover {
            background: var(--primary-dark);
        }
        .error { 
            color: #c62828; 
            background: #fde8e8;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 13px;
        }
        h2 {
            margin-top: 0;
            color: var(--text);
        }
        h3 {
            font-size: 16px;
            color: var(--primary-dark);
            background: var(--primary-light);
            padding: 12px;
            border-radius: 8px;
            margin: 15px 0 20px 0;
            border-left: 4px solid var(--primary);
        }
        p {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 10px;
            line-height: 1.5;
        }
    </style>
</head>
<body>

<div class="card">

    <h2>Verifikasi Keamanan</h2>

    <p>Jawab pertanyaan keamanan akun Anda di bawah ini:</p>

    <h3>{{ $question }}</h3>

    @if ($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.verify') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Jawaban Anda</label>
            <input type="password"
                   name="security_answer"
                   id="security_answer"
                   required
                   autocomplete="off"
                   placeholder="Masukkan jawaban">
            <button type="button" class="toggle-password" onclick="togglePassword('security_answer', 'icon-answer')">
                <!-- Ikon Mata Tertutup (Default) -->
                <svg id="icon-answer" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
            </button>
        </div>

        <button type="submit">
            Verifikasi Jawaban
        </button>
    </form>

</div>

<script>
    function togglePassword(fieldId, iconId) {
        const inputField = document.getElementById(fieldId);
        const iconElement = document.getElementById(iconId);

        const eyeOpen = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        const eyeClosed = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';

        if (inputField.type === "password") {
            inputField.type = "text";
            iconElement.innerHTML = eyeOpen;
        } else {
            inputField.type = "password";
            iconElement.innerHTML = eyeClosed;
        }
    }
</script>

</body>
</html>