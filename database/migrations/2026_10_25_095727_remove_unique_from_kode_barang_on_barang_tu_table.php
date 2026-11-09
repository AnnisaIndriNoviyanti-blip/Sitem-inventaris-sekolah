
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_tu', function (Blueprint $table) {
            $table->dropUnique('barang_tu_kode_barang_unique');
        });
    }

    public function down(): void
    {
        Schema::table('barang_tu', function (Blueprint $table) {
            $table->unique('kode_barang');
        });
    }
};
