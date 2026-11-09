
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_tu', function (Blueprint $table) {
            $table->string('jenis_barang', 30)
                ->nullable()
                ->after('kode_barang');
        });
    }

    public function down(): void
    {
        Schema::table('barang_tu', function (Blueprint $table) {
            $table->dropColumn('jenis_barang');
        });
    }
};
