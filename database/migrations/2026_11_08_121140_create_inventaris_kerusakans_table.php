<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris_kerusakan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barang_tu_id')
                ->constrained('barang_tu')
                ->restrictOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('tanggal_kerusakan');

            $table->enum('tingkat_kerusakan', [
                'rusak_ringan',
                'rusak_berat',
            ]);

            $table->text('catatan');

            $table->string('foto_bukti');

            $table->enum('status_penanganan', [
                'belum_diperbaiki',
                'sedang_diperbaiki',
                'selesai',
            ])->default('belum_diperbaiki');

            $table->timestamps();

            $table->index(['barang_tu_id', 'status_penanganan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris_kerusakan');
    }
};