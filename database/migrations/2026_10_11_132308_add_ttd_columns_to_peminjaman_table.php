<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->string('ttd_peminjam')->nullable();
            $table->timestamp('ttd_peminjaman_at')->nullable();
            $table->timestamp('ttd_pengembalian_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn([
                'ttd_peminjam',
                'ttd_peminjaman_at',
                'ttd_pengembalian_at',
            ]);
        });
    }
};
