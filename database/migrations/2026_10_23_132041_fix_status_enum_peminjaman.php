<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
        ALTER TABLE peminjaman
        MODIFY status ENUM(
            'menunggu',
            'disetujui',
            'ditolak',
            'dipinjam',
            'dikembalikan',
            'terlambat'
        ) NOT NULL DEFAULT 'menunggu'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
