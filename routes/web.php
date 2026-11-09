<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangTuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\LaporanKerusakanController;
use App\Http\Controllers\InventarisKerusakanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ManajemenAkunController;
use App\Http\Controllers\KelasDashboardController;
use App\Http\Controllers\OrganisasiDashboardController;
use App\Http\Controllers\AdminRiwayatController;
use App\Http\Controllers\AdminSecurityController;


// Homepage SISKO
Route::get('/', function () {
    return view('welcome');
    })->name('home');

Route::get('/daftar', function () {
    return view('daftar');
    })->name('daftar');



Route::middleware('guest')->group(function () {

    // Login SISKO yang sudah ada.
    Route::get('/masuk', [
        AuthController::class,
        'showLogin'
    ])->name('login');

    Route::post('/masuk', [
        AuthController::class,
        'login'
    ])->name('login.store');

    // Halaman awal lupa password.
    Route::get('/lupa-password', [
        AdminSecurityController::class,
        'forgotForm'
    ])->name('password.request');

    // Meminta pertanyaan keamanan.
    Route::post('/lupa-password/pertanyaan', [
        AdminSecurityController::class,
        'showQuestion'
    ])->name('password.question');

    // Memeriksa jawaban keamanan.
    Route::post('/lupa-password/verifikasi', [
        AdminSecurityController::class,
        'verifyAnswer'
    ])->name('password.verify');

    // Formulir password baru.
    Route::get('/lupa-password/buat-password', [
        AdminSecurityController::class,
        'resetForm'
    ])->name('password.reset.form');

    // Menyimpan password baru.
    Route::post('/lupa-password/buat-password', [
        AdminSecurityController::class,
        'resetPassword'
    ])->name('password.reset');
});


Route::post('/keluar', [
    AuthController::class,
    'logout'
])->middleware('auth')->name('logout');

Route::get('/admin/dashboard', [
    AdminDashboardController::class,
    'index'
])->middleware('auth')->name('admin.dashboard');


Route::middleware('auth')->group(function(){
    Route::get(
        '/organisasi/dashboard',
        [OrganisasiDashboardController::class, 'index']
    )->name('organisasi.dashboard');
});
  

Route::middleware('auth')->group(function () {
     Route::get('/peminjaman', [
        PeminjamanController::class, 'index'
    ])->name('peminjaman.index');

    /*
    |--------------------------------------------------------------------------
    | PEMINJAMAN BARANG TU
    |--------------------------------------------------------------------------
    | Admin TU dapat melihat daftar peminjaman untuk dikelola.
    | Guru dan siswa dapat melihat peminjaman sesuai hak aksesnya.
    | Controller tetap harus memfilter data berdasarkan pengguna.
    */

    Route::get('/peminjaman', [
        PeminjamanController::class, 'index'
    ])->name('peminjaman.index');

    Route::get('/peminjaman/{peminjaman}', [
        PeminjamanController::class, 'show'
    ])->name('peminjaman.show');
    Route::get('/laporan-kerusakan', [
    LaporanKerusakanController::class, 'index'
    ])->name('laporan-kerusakan.index');


    Route::middleware('role:guru,siswa')->group(function () {

    // Katalog inventaris yang dapat dipilih oleh peminjam.
Route::get('/peminjam/katalog', [
    PeminjamanController::class,
    'katalog'
])->name('peminjaman.katalog');

        // Daftar peminjaman
        Route::get('/peminjaman', [
        PeminjamanController::class, 'index'
    ])->name('peminjaman.index');
    
    // Form tambah harus didefinisikan sebelum route detail
    Route::middleware('role:guru,siswa')->group(function () {
         Route::get('/peminjaman/create', [
            PeminjamanController::class, 'create'
        ])->name('peminjaman.create');

        Route::post('/peminjaman', [
            PeminjamanController::class, 'store'
        ])->name('peminjaman.store');
    });

    // Route detail dibatasi hanya untuk ID numerik
    Route::get('/peminjaman/{peminjaman}', [
        PeminjamanController::class, 'show'
    ])
        ->whereNumber('peminjaman')
        ->name('peminjaman.show');

    // Pertahankan route laporan kerusakan dan route lainnya
       


        // Membuat laporan kerusakan dari peminjaman milik sendiri.

        Route::get(
            '/peminjaman/{peminjaman}/laporan-kerusakan/create',
            [LaporanKerusakanController::class, 'create']
        )->name('laporan-kerusakan.create');

        Route::post(
            '/peminjaman/{peminjaman}/laporan-kerusakan',
            [LaporanKerusakanController::class, 'store']
        )->name('laporan-kerusakan.store');

        
        Route::middleware('role:guru,siswa')->group(function () {

            Route::get('/kelas', [
                KelasDashboardController::class,
                'index'
                ])->name('kelas.dashboard');

        });
    });


    /*
    |--------------------------------------------------------------------------
    | FITUR ADMIN TU
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin_tu')->group(function () {

    // Pengaturan keamanan Admin TU.
Route::get('/admin/pengamanan', [
    AdminSecurityController::class,
    'edit'
])->name('admin.pengamanan.index');

Route::post('/admin/pengamanan', [
    AdminSecurityController::class,
    'update'
])->name('admin.pengamanan.update');

Route::post('/admin/pengamanan/reset', [AdminSecurityController::class, 'resetSecurity'])
    ->name('admin.pengamanan.reset');
    

    // Manajemen akun
        Route::resource('admin/akun', ManajemenAkunController::class)
    ->names('admin.akun')
    ->except(['show', 'destroy']);
    
    Route::patch(
        '/admin/akun/{akun}/status',
        [ManajemenAkunController::class, 'ubahStatus']
        )->name('admin.akun.status');

        Route::post(
    '/admin/akun/{akun}/terbitkan-password',
    [ManajemenAkunController::class, 'terbitkanPassword']
)->name('admin.akun.terbitkan-password');

        // Mengelola data barang TU.

        Route::resource('barang-tu', BarangTuController::class)
            ->except(['show']);


        // Memproses pengajuan peminjaman.

        Route::post('/peminjaman/{peminjaman}/proses', [
            PeminjamanController::class, 'proses'
        ])->name('peminjaman.proses');


        // Mengonfirmasi pengembalian barang.

        Route::post('/peminjaman/{peminjaman}/kembalikan', [
            PeminjamanController::class, 'kembalikan'
        ])->name('peminjaman.kembalikan');
        Route::get('/admin/pengembalian', [
    PeminjamanController::class,
    'pengembalianIndex'
])->name('admin.pengembalian.index');


        // Memperbarui status laporan kerusakan.

        Route::patch('/laporan-kerusakan/{laporanKerusakan}/status', [
            LaporanKerusakanController::class, 'updateStatus'
        ])->name('laporan-kerusakan.status');
        
        Route::get('/admin/peminjaman', [
        PeminjamanController::class,
        'verifikasi'
    ])->name('admin.peminjaman.index');

    // Route cetak buku peminjaman
    Route::get('/admin/peminjaman/cetak', [
        PeminjamanController::class,
        'cetakBuku'
    ])->name('admin.peminjaman.cetak');

    
Route::get('/admin/peminjaman/export-excel', [
    PeminjamanController::class,
    'exportBuku'
])->name('admin.peminjaman.export');

// Download laporan kerusakan dalam format Excel
Route::get('/admin/kerusakan/export-excel', [
    LaporanKerusakanController::class,
    'exportExcel'
])->name('admin.kerusakan.export');


// ==================================================
// INVENTARIS BARANG RUSAK - ADMIN TU
// ==================================================

Route::get('/admin/kerusakan/inventaris', [
    InventarisKerusakanController::class,
    'index',
])->name('admin.kerusakan.inventaris.index');

Route::get('/admin/kerusakan/inventaris/tambah', [
    InventarisKerusakanController::class,
    'create',
])->name('admin.kerusakan.inventaris.create');

Route::post('/admin/kerusakan/inventaris', [
    InventarisKerusakanController::class,
    'store',
])->name('admin.kerusakan.inventaris.store');

Route::patch('/admin/kerusakan/inventaris/{inventarisKerusakan}/penanganan', [
    InventarisKerusakanController::class,
    'updatePenanganan',
])->name('admin.kerusakan.inventaris.penanganan');

Route::get('/admin/kerusakan/inventaris/export-excel', [
    InventarisKerusakanController::class,
    'exportExcel',
])->name('admin.kerusakan.inventaris.export');

// Halaman pengelolaan laporan kerusakan
Route::get('/admin/kerusakan', [
    LaporanKerusakanController::class,
    'adminIndex',
])->name('admin.kerusakan.index');

// PDF laporan kerusakan inventaris
Route::get('/admin/kerusakan/inventaris/export-pdf', [
    InventarisKerusakanController::class,
    'exportLaporanKerusakan',
])->name('admin.kerusakan.inventaris.export-pdf');


// PDF laporan penanganan inventaris
Route::get('/admin/kerusakan/penanganan/export-pdf', [
    InventarisKerusakanController::class,
    'exportLaporanPenanganan',
])->name('admin.kerusakan.penanganan.export-pdf');

Route::get('/admin/kerusakan/inventaris/export-pdf', [
        InventarisKerusakanController::class,
        'exportLaporanKerusakan',
    ])->name('admin.kerusakan.inventaris.export-pdf');

    Route::get('/admin/kerusakan/penanganan/export-pdf', [
        InventarisKerusakanController::class,
        'exportLaporanPenanganan',
    ])->name('admin.kerusakan.penanganan.export-pdf');




// Riwayat dan rekap peminjaman
Route::get('/admin/riwayat', [
    AdminRiwayatController::class,
    'index'
])->name('admin.riwayat.index');



    });

});
