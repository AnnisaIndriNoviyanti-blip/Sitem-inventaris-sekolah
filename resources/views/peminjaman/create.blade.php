
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form Peminjaman | SISKO</title>

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
            max-width: 750px;
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

        input, select, textarea {
            box-sizing: border-box;
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd9d0;
            border-radius: 8px;
            background: white;
            color: #183b2c;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        button, .btn {
            display: inline-block;
            margin-top: 16px;
            background: #176b45;
            color: white;
            border: 0;
            border-radius: 8px;
            padding: 12px 16px;
            text-decoration: none;
            cursor: pointer;
        }

        button:hover, .btn:hover {
            background: #125638;
        }

        .btn-hapus {
            background: #b42318;
            margin-top: 8px;
        }

        .btn-hapus:hover {
            background: #8f1d14;
        }

        .error {
            color: #b42318;
            font-size: 14px;
        }

        .signature-canvas-wrapper {
            width: 100%;
            border: 1px solid #ccd9d0;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
        }

        #signatureCanvas {
            display: block;
            width: 100%;
            height: 190px;
            touch-action: none;
            cursor: crosshair;
        }

        .hint {
            display: block;
            margin-top: 6px;
            color: #65766b;
            font-size: 12px;
        }

        .barang-row {
            border: 1px solid #e4ebe6;
            border-radius: 10px;
            padding: 14px;
            margin-top: 12px;
        }

        .section-title {
            margin-top: 28px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e4ebe6;
        }

        @media (max-width: 600px) {
            body {
                padding: 12px;
            }

            .panel {
                padding: 18px;
            }
        }
    </style>
</head>

<body>
<main>
    <p>
        <a href="{{ route('peminjaman.index') }}">
            ← Kembali ke daftar peminjaman
        </a>
    </p>

    <section class="panel">
        <h1>Form Peminjaman Barang TU</h1>
        <p>Isi data peminjaman dengan benar sebelum mengirim pengajuan.</p>

        @if ($errors->any())
            <div class="error">
                <strong>Periksa kembali data berikut:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            action="{{ route('peminjaman.store') }}"
            method="POST"
            id="loanCreateForm"
        >
            @csrf

            @if (auth()->user()->jenis_akun === 'kelas')
                <label for="nama_peminjam">
                    Nama Guru yang Bertanggung Jawab
                </label>

                <input
                    id="nama_peminjam"
                    name="nama_peminjam"
                    type="text"
                    value="{{ old('nama_peminjam') }}"
                    maxlength="255"
                    placeholder="Masukkan nama guru"
                    required
                >

                <small class="hint">
                    Isi nama guru yang mengajar dan bertanggung jawab
                    atas peminjaman barang.
                </small>
            @else
                <label>Nama Organisasi / Ekskul</label>
                <input
                    type="text"
                    value="{{ auth()->user()->organisasi?->nama_organisasi ?? auth()->user()->nama }}"
                    readonly
                >

                <small class="hint">
                    Nama diambil otomatis dari akun yang sedang login.
                </small>
            @endif

            <label for="tanggal_pinjam">Tanggal Peminjaman</label>
            <input
                id="tanggal_pinjam"
                name="tanggal_pinjam"
                type="date"
                value="{{ old('tanggal_pinjam') }}"
                required
            >

            <label for="tanggal_kembali">Tanggal Rencana Pengembalian</label>
            <input
                id="tanggal_kembali"
                name="tanggal_kembali"
                type="date"
                value="{{ old('tanggal_kembali') }}"
                required
            >

            <label for="keperluan">Keperluan Peminjaman</label>
            <textarea
                id="keperluan"
                name="keperluan"
                maxlength="1000"
                required
                placeholder="Contoh: Pembelajaran di kelas"
            >{{ old('keperluan') }}</textarea>

            <h3 class="section-title">Barang yang Dipinjam</h3>
            <p class="hint">
                Pilih barang dan masukkan jumlahnya. Tambahkan baris
                jika ingin meminjam jenis barang lainnya.
            </p>

            <div id="daftar-barang">
                <div class="barang-row">
                    <label>Nama Barang</label>
                    <select name="barang[0][barang_tu_id]" required>
                        <option value="">-- Pilih barang --</option>

                        @foreach ($barang as $item)

    <option value="{{ $item->id }}"
        {{ (string) old(
            'barang.0.barang_tu_id',
            $barangTerpilih ?? ''
        ) === (string) $item->id ? 'selected' : '' }}>

        {{ $item->nama_barang }}

    </option>

@endforeach
                    </select>

                    <label>Jumlah</label>
                    <input
                        name="barang[0][jumlah]"
                        type="number"
                        min="1"
                        value="1"
                        required
                    >
                </div>
            </div>

            <button type="button" id="tambah-barang">
                + Tambah Barang
            </button>

            <label for="catatan">Catatan Tambahan (Opsional)</label>
            <textarea
                id="catatan"
                name="catatan"
                maxlength="2000"
                placeholder="Tambahkan informasi jika diperlukan"
            >{{ old('catatan') }}</textarea>

            <label for="signatureCanvas">Tanda Tangan Peminjam</label>
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
                id="ttd_peminjam_data"
                name="ttd_peminjam_data"
                value=""
            >
            <small class="hint">Gambarkan tanda tangan langsung pada area di atas.</small>
            <button type="button" id="clearSignature" class="btn-hapus">
                Hapus Tanda Tangan
            </button>

            <button type="submit">Kirim Pengajuan</button>
        </form>
    </section>
</main>

<script>

    const loanCreateForm = document.getElementById('loanCreateForm');
    const signatureCanvas = document.getElementById('signatureCanvas');
    const signatureInput = document.getElementById('ttd_peminjam_data');
    const clearSignatureButton = document.getElementById('clearSignature');
    const signatureContext = signatureCanvas.getContext('2d');
    let signatureStrokes = [];
    let activeSignatureStroke = null;
    let isDrawingSignature = false;

    signatureContext.strokeStyle = '#172033';
    signatureContext.lineWidth = 3;
    signatureContext.lineCap = 'round';
    signatureContext.lineJoin = 'round';

    function getSignaturePosition(event) {
        const rect = signatureCanvas.getBoundingClientRect();
        return {
            x: (event.clientX - rect.left) * (signatureCanvas.width / rect.width),
            y: (event.clientY - rect.top) * (signatureCanvas.height / rect.height)
        };
    }

    signatureCanvas.addEventListener('pointerdown', function (event) {
        event.preventDefault();
        isDrawingSignature = true;
        signatureCanvas.setPointerCapture(event.pointerId);
        const point = getSignaturePosition(event);
        activeSignatureStroke = [point];
        signatureStrokes.push(activeSignatureStroke);
        signatureContext.beginPath();
        signatureContext.moveTo(point.x, point.y);
        signatureContext.lineTo(point.x + 0.1, point.y + 0.1);
        signatureContext.stroke();
    });

    signatureCanvas.addEventListener('pointermove', function (event) {
        if (!isDrawingSignature) return;
        event.preventDefault();
        const point = getSignaturePosition(event);
        activeSignatureStroke.push(point);
        signatureContext.lineTo(point.x, point.y);
        signatureContext.stroke();
    });

    function stopSignatureDrawing() {
        isDrawingSignature = false;
        activeSignatureStroke = null;
        signatureContext.beginPath();
    }

    signatureCanvas.addEventListener('pointerup', stopSignatureDrawing);
    signatureCanvas.addEventListener('pointercancel', stopSignatureDrawing);
    signatureCanvas.addEventListener('lostpointercapture', stopSignatureDrawing);

    clearSignatureButton.addEventListener('click', function () {
        signatureContext.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
        signatureStrokes = [];
        activeSignatureStroke = null;
        signatureInput.value = '';
    });

    loanCreateForm.addEventListener('submit', function (event) {
        if (signatureStrokes.length === 0) {
            event.preventDefault();
            alert('Silakan gambar tanda tangan terlebih dahulu.');
            return;
        }

        signatureInput.value = JSON.stringify({
            width: signatureCanvas.width,
            height: signatureCanvas.height,
            strokes: signatureStrokes
        });
    });

    const daftarBarang = document.getElementById('daftar-barang');
    const tombolTambah = document.getElementById('tambah-barang');

    let nomorBarang = 1;

    tombolTambah.addEventListener('click', function () {
        const baris = document.createElement('div');
        baris.className = 'barang-row';

        baris.innerHTML = `
            <label>Nama Barang</label>
            <select name="barang[${nomorBarang}][barang_tu_id]" required>
                <option value="">-- Pilih barang --</option>
                @foreach ($barang as $item)
                    <option value="{{ $item->id }}">
                        {{ $item->nama_barang }}
                    </option>
                @endforeach
            </select>

            <label>Jumlah</label>
            <input
                name="barang[${nomorBarang}][jumlah]"
                type="number"
                min="1"
                value="1"
                required
            >

            <button type="button" class="btn-hapus">
                Hapus Baris
            </button>
        `;

        baris.querySelector('.btn-hapus').addEventListener(
            'click',
            function () {
                baris.remove();
            }
        );

        daftarBarang.appendChild(baris);
        nomorBarang++;
    });
</script>
</body>
</html>
