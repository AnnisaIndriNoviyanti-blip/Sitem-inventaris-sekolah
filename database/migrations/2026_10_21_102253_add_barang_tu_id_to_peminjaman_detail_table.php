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
        Schema::table('peminjaman_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('barang_tu_id')
            ->nullable()
            ->after('peminjaman_id');
            
            $table->foreign('barang_tu_id')
            ->references('id')
            ->on('barang_tu')
            ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman_detail', function (Blueprint $table) {
            $table->dropForeign(['barang_tu_id']);
            $table->dropColumn(['barang_tu_id']);
        });
    }
};
