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
        Schema::create('laporan_kerusakan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('peminjaman_id')
            ->constrained('peminjaman')
            ->restrictOnDelete();

            $table->foreignId('peminjaman_detail_id')
            ->nullable()
            ->constrained('peminjaman_detail')
            ->nullOnDelete();

            $table->date('tanggal_laporan');
            $table->string('nomor_whatsapp', 20)->nullable();
            $table->string('bukti_laporan')->nullable();

            $table->enum('status', [
                'menunggu',
                'diproses',
                'selesai',
                'ditolak'
            ])->default('menunggu');

            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_kerusakan');
    }
};
