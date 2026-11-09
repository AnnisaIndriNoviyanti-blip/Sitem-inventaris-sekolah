<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE laporan_kerusakan
            MODIFY COLUMN status
            ENUM('menunggu', 'diproses', 'selesai', 'ditolak')
            NOT NULL DEFAULT 'menunggu'
        ");
    }

    public function down(): void
    {
        // Tidak mengubah kembali enum agar data laporan
        // yang sudah tersimpan tidak kehilangan statusnya.
    }
};