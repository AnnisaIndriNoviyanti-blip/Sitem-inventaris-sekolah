<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ManajemenAkunController extends Controller
{
    private function pastikanAdmin(): void
    {
        abort_unless(
            auth()->check() &&
            auth()->user()->role === 'admin_tu',
            403
        );
    }

    public function index()
    {
        $this->pastikanAdmin();

        $akun = User::with(['kelas', 'organisasi'])
            ->orderBy('nama')
            ->paginate(10);
            foreach ($akun as $user) {

    $user->password_aktif = $user->password_encrypted
        ? Crypt::decryptString($user->password_encrypted)
        : null;

}
        return view('admin.akun.index', compact('akun'));
    }

    public function create()
    {
        $this->pastikanAdmin();

        $kelas = Kelas::where('aktif', true)
            ->orderBy('nama_kelas')
            ->get();

        $organisasi = Organisasi::where('aktif', true)
            ->orderBy('nama_organisasi')
            ->get();

        return view(
            'admin.akun.create',
            compact('kelas', 'organisasi')
        );
    }

    
    
public function store(Request $request)
{
    $this->pastikanAdmin();

    // Rapikan spasi di awal dan akhir nama.
    $request->merge([
        'nama' => trim((string) $request->input('nama')),
    ]);

    $jenisAkun = $request->input('jenis_akun');

    // Aturan dasar untuk semua akun.
    $aturan = [
        'nama' => ['required', 'string', 'max:255'],

        'username' => [
            'required',
            'string',
            'max:255',
            'unique:users,username',
        ],

        'password' => [
            'required',
            'string',
            'min:8',
        ],

        'jenis_akun' => [
            'required',
            Rule::in(['kelas', 'organisasi']),
        ],

        'kelas_id' => [
            'nullable',
            'required_if:jenis_akun,kelas',
            'exists:kelas,id',
        ],

        'kategori' => [
            'nullable',
            'required_if:jenis_akun,organisasi',
            Rule::in(['organisasi', 'ekskul']),
        ],
    ];

    // Nama organisasi/ekskul harus unik.
    // Aturan ini hanya berlaku untuk akun organisasi.
    if ($jenisAkun === 'organisasi') {
        $aturan['nama'][] = Rule::unique(
            'organisasi',
            'nama_organisasi'
        );
    }

    $data = $request->validate(
        $aturan,
        [
            'nama.unique' =>
                'Nama organisasi atau ekskul sudah digunakan. '
                . 'Silakan gunakan nama lain.',

            'kategori.required_if' =>
                'Silakan pilih kategori organisasi atau ekskul.',

            'kelas_id.required_if' =>
                'Silakan pilih tingkatan kelas.',
        ]
    );

    // Siapkan ID relasi.
    $kelasId = null;
    $organisasiId = null;

    if ($data['jenis_akun'] === 'kelas') {
        // Pastikan kelas yang dipilih masih aktif.
        $kelasAktif = Kelas::where('aktif', true)
            ->whereKey($data['kelas_id'])
            ->exists();

        if (!$kelasAktif) {
            return back()
                ->withErrors([
                    'kelas_id' => 'Pilih kelas yang masih aktif.',
                ])
                ->withInput();
        }

        $kelasId = $data['kelas_id'];
    }

    try {
        // Simpan data organisasi dan akun dalam satu transaksi.
        // Jika salah satunya gagal, perubahan dibatalkan.
        DB::transaction(function () use (
            $data,
            $kelasId,
            &$organisasiId
        ) {
            if ($data['jenis_akun'] === 'organisasi') {
                // Buat data organisasi/ekskul baru otomatis.
                $organisasi = Organisasi::create([
                    'nama_organisasi' => $data['nama'],
                    'jenis' => $data['kategori'],
                    'aktif' => true,
                ]);

                $organisasiId = $organisasi->id;
            }

            // Buat akun login.
            User::create([
    'nama' => $data['nama'],
    'username' => $data['username'],

    'password' => $data['password'],

    'password_encrypted' => Crypt::encryptString(
        $data['password']
    ),

    'password_rotated_at' => now(),

    'role' => $data['jenis_akun'] === 'kelas'
        ? 'guru'
        : 'siswa',

    'status' => 'aktif',
    'jenis_akun' => $data['jenis_akun'],
    'kelas_id' => $kelasId,
    'organisasi_id' => $organisasiId,
]);
        });
    } catch (QueryException $e) {
        // Jika ada nama duplikat yang masuk bersamaan,
        // database tetap menjadi penjaga terakhir.
        $pesan = str_contains(
            strtolower($e->getMessage()),
            'unique'
        ) || str_contains(
            strtolower($e->getMessage()),
            'duplicate'
        );

        if ($pesan) {
            return back()
                ->withErrors([
                    'nama' => $data['jenis_akun'] === 'organisasi'
                        ? 'Nama organisasi atau ekskul sudah digunakan.'
                        : 'Data akun sudah digunakan. Periksa nama dan username.',
                ])
                ->withInput();
        }

        // Jangan menyembunyikan error database lainnya.
        throw $e;
    }

    return redirect()
        ->route('admin.akun.index')
        ->with('success', 'Akun berhasil dibuat.');
}


public function edit(User $akun)
{
    $this->pastikanAdmin();

    $akun->load(['kelas', 'organisasi']);

    $kelas = Kelas::where('aktif', true)
        ->orderBy('nama_kelas')
        ->get();

    $organisasi = Organisasi::where('aktif', true)
        ->orderBy('nama_organisasi')
        ->get();

    return view(
        'admin.akun.edit',
        compact('akun', 'kelas', 'organisasi')
    );
}

public function update(Request $request, User $akun)
{
    $this->pastikanAdmin();

    $request->merge([
        'nama' => trim((string) $request->input('nama')),
    ]);

    $jenisAkun = $request->input('jenis_akun');

    $aturan = [
        'nama' => ['required', 'string', 'max:255'],

        'username' => [
            'required',
            'string',
            'max:255',
            Rule::unique('users', 'username')->ignore($akun->id),
        ],

        'jenis_akun' => [
            'required',
            Rule::in(['kelas', 'organisasi']),
        ],

        'kelas_id' => [
            'nullable',
            'required_if:jenis_akun,kelas',
            'exists:kelas,id',
        ],

        'kategori' => [
            'nullable',
            'required_if:jenis_akun,organisasi',
            Rule::in(['organisasi', 'ekskul']),
        ],

        // Password boleh dikosongkan jika tidak ingin diganti.
        'password' => [
            'nullable',
            'string',
            'min:8',
        ],
    ];

    if ($jenisAkun === 'organisasi') {
        $aturan['nama'][] = Rule::unique(
            'organisasi',
            'nama_organisasi'
        )->ignore($akun->organisasi_id);
    }

    $data = $request->validate($aturan, [
        'nama.unique' =>
            'Nama organisasi atau ekskul sudah digunakan.',

        'username.unique' =>
            'Username sudah digunakan akun lain.',

        'kategori.required_if' =>
            'Silakan pilih kategori organisasi atau ekskul.',

        'kelas_id.required_if' =>
            'Silakan pilih tingkatan kelas.',
    ]);

    $kelasId = null;
    $organisasiId = null;

    if ($data['jenis_akun'] === 'kelas') {
        $kelasAktif = Kelas::where('aktif', true)
            ->whereKey($data['kelas_id'])
            ->exists();

        if (!$kelasAktif) {
            return back()
                ->withErrors([
                    'kelas_id' => 'Pilih kelas yang masih aktif.',
                ])
                ->withInput();
        }

        $kelasId = $data['kelas_id'];
    }

    DB::transaction(function () use (
        $data,
        $akun,
        $kelasId,
        &$organisasiId
    ) {
        if ($data['jenis_akun'] === 'organisasi') {
            // Gunakan organisasi lama jika akun sudah terhubung.
            if ($akun->organisasi_id !== null) {
                $organisasi = Organisasi::findOrFail(
                    $akun->organisasi_id
                );

                $organisasi->update([
                    'nama_organisasi' => $data['nama'],
                    'jenis' => $data['kategori'],
                    'aktif' => true,
                ]);
            } else {
                // Buat organisasi jika akun sebelumnya bertipe kelas.
                $organisasi = Organisasi::create([
                    'nama_organisasi' => $data['nama'],
                    'jenis' => $data['kategori'],
                    'aktif' => true,
                ]);
            }

            $organisasiId = $organisasi->id;
        }

        $perubahan = [
    'nama' => $data['nama'],
    'username' => $data['username'],

    'role' => $data['jenis_akun'] === 'kelas'
        ? 'guru'
        : 'siswa',

    'jenis_akun' => $data['jenis_akun'],
    'kelas_id' => $kelasId,
    'organisasi_id' => $organisasiId,
];

        if (!empty($data['password'])) {

    $perubahan['password'] = $data['password'];

    $perubahan['password_encrypted'] =
        Crypt::encryptString($data['password']);

    $perubahan['password_rotated_at'] = now();

}

        $akun->update($perubahan);
    });

    return redirect()
        ->route('admin.akun.index')
        ->with('success', 'Akun berhasil diperbarui.');
}



public function terbitkanPassword(User $akun)
{
    $this->pastikanAdmin();

    abort_unless(
        in_array($akun->jenis_akun, ['kelas', 'organisasi'], true),
        404
    );

    // Membuat password acak 12 karakter.
    $passwordBaru = Str::random(12);

    $akun->update([
        'password' => $passwordBaru,

        'password_encrypted' => Crypt::encryptString(
            $passwordBaru
        ),

        'password_rotated_at' => now(),
    ]);

    return redirect()
        ->route('admin.akun.index')
        ->with(
            'success',
            'Password berhasil diterbitkan.'
        );
}


public function ubahStatus(User $akun)
{
    $this->pastikanAdmin();

    // Jangan izinkan pengelolaan akun Admin TU melalui menu ini.
    if ($akun->role === 'admin_tu') {
        return redirect()
            ->route('admin.akun.index')
            ->withErrors([
                'akun' => 'Status akun Admin TU tidak dapat diubah melalui menu ini.',
            ]);
    }

    // Hanya izinkan status yang dikenal.
    if (!in_array($akun->status, ['aktif', 'nonaktif'], true)) {
        return redirect()
            ->route('admin.akun.index')
            ->withErrors([
                'akun' => 'Status akun tidak valid.',
            ]);
    }

    // Balik status akun tanpa menghapus data apa pun.
    $statusBaru = $akun->status === 'aktif'
        ? 'nonaktif'
        : 'aktif';

    $akun->update([
        'status' => $statusBaru,
    ]);

    $pesan = $statusBaru === 'nonaktif'
        ? 'Akun berhasil dinonaktifkan. Riwayat transaksi tetap tersimpan.'
        : 'Akun berhasil diaktifkan kembali.';

    return redirect()
        ->route('admin.akun.index')
        ->with('success', $pesan);
}



public function destroy(User $akun)
{
    $this->pastikanAdmin();

    // Lindungi akun admin TU.
    if ($akun->role === 'admin_tu') {
        return redirect()
            ->route('admin.akun.index')
            ->withErrors([
                'akun' => 'Akun Admin TU tidak dapat dihapus melalui menu ini.',
            ]);
    }

    // Lindungi riwayat peminjaman.
    if ($akun->peminjaman()->exists()) {
        return redirect()
            ->route('admin.akun.index')
            ->withErrors([
                'akun' => 'Akun tidak dapat dihapus karena masih memiliki riwayat peminjaman.',
            ]);
    }

    $akun->delete();

    return redirect()
        ->route('admin.akun.index')
        ->with('success', 'Akun berhasil dihapus.');
}

}
