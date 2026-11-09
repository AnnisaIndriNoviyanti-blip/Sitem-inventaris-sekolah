
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Akun | SISKO</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f5;
            color: #183b2c;
            margin: 0;
            padding: 24px;
        }

        main {
            max-width: 650px;
            margin: auto;
        }

        .panel {
            background: white;
            padding: 26px;
            border-radius: 16px;
        }

        label {
            display: block;
            margin: 17px 0 7px;
            font-weight: 600;
        }

        input, select {
            box-sizing: border-box;
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd9d0;
            border-radius: 8px;
            background: white;
        }

        button {
            margin-top: 22px;
            background: #176b45;
            color: white;
            border: 0;
            border-radius: 8px;
            padding: 13px 18px;
            cursor: pointer;
        }

        button:hover {
            background: #125638;
        }

        .error {
            color: #b42318;
            font-size: 14px;
        }

        .hint {
            display: block;
            margin-top: 6px;
            color: #65766b;
            font-size: 12px;
            line-height: 1.5;
        }

        [hidden] {
            display: none !important;
        }
    </style>
</head>

<body>
<main>
    <p>
        <a href="{{ route('admin.akun.index') }}">
            ← Kembali ke daftar akun
        </a>
    </p>

    <section class="panel">
        <h1>Tambah Akun Baru</h1>
        <p>Buat akun untuk kelas atau organisasi sekolah.</p>

        @if($errors->any())
            <div class="error">
                <p>Periksa kembali data berikut:</p>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.akun.store') }}" method="POST">
            @csrf

            <label for="nama">Nama Jurusan/Organisasi</label>
            <input
                id="nama"
                name="nama"
                type="text"
                value="{{ old('nama') }}"
                placeholder="Contoh: RPL A, OSIS, atau Coding Club"
                maxlength="255"
                required
            >
            <small class="hint">
                Untuk organisasi/ekskul, nama boleh baru dan belum pernah terdaftar.
            </small>

            <label for="username">Username</label>
            <input
                id="username"
                name="username"
                type="text"
                value="{{ old('username') }}"
                autocomplete="off"
                maxlength="255"
                required
            >

            <label for="password">Password Awal</label>
            <input
                id="password"
                name="password"
                type="password"
                minlength="8"
                autocomplete="new-password"
                required
            >
            <small class="hint">Minimal 8 karakter.</small>

            <label for="jenis_akun">Jenis Akun</label>
            <select name="jenis_akun" id="jenis_akun" required>
                <option value="">-- Pilih jenis akun --</option>
                <option value="kelas"
                    {{ old('jenis_akun') === 'kelas' ? 'selected' : '' }}>
                    Kelas
                </option>
                <option value="organisasi"
                    {{ old('jenis_akun') === 'organisasi' ? 'selected' : '' }}>
                    Organisasi / Ekskul
                </option>
            </select>

            <div id="pilihan-kelas" hidden>
                <label for="kelas_id">Pilih Tingkatan Kelas</label>
                <select name="kelas_id" id="kelas_id">
                    <option value="">-- Pilih tingkatan --</option>

                    @foreach($kelas as $item)
                        <option
                            value="{{ $item->id }}"
                            {{ (string) old('kelas_id') === (string) $item->id
                                ? 'selected' : '' }}
                        >
                            {{ $item->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="pilihan-kategori" hidden>
                <label for="kategori">Kategori</label>
                <select name="kategori" id="kategori">
                    <option value="">-- Pilih kategori --</option>
                    <option value="organisasi"
                        {{ old('kategori') === 'organisasi' ? 'selected' : '' }}>
                        Organisasi
                    </option>
                    <option value="ekskul"
                        {{ old('kategori') === 'ekskul' ? 'selected' : '' }}>
                        Ekskul
                    </option>
                </select>
                <small class="hint">
                    Pilih Organisasi untuk OSIS/MPK atau Ekskul untuk kegiatan ekstrakurikuler.
                </small>
            </div>

            <button type="submit">Simpan Akun</button>
        </form>
    </section>
</main>

<script>
    const jenis = document.getElementById('jenis_akun');

    const pilihanKelas = document.getElementById('pilihan-kelas');
    const kelas = document.getElementById('kelas_id');

    const pilihanKategori = document.getElementById('pilihan-kategori');
    const kategori = document.getElementById('kategori');

    function aturPilihan() {
        const untukKelas = jenis.value === 'kelas';
        const untukOrganisasi = jenis.value === 'organisasi';

        pilihanKelas.hidden = !untukKelas;
        pilihanKategori.hidden = !untukOrganisasi;

        kelas.required = untukKelas;
        kategori.required = untukOrganisasi;

        if (!untukKelas) {
            kelas.value = '';
        }

        if (!untukOrganisasi) {
            kategori.value = '';
        }
    }

    jenis.addEventListener('change', aturPilihan);

    aturPilihan();
</script>
</body>
</html>
