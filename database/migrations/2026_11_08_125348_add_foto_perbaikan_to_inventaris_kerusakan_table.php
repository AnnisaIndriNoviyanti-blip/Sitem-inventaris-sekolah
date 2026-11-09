
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventaris_kerusakan', function (Blueprint $table) {
            $table->string('foto_perbaikan')
                ->nullable()
                ->after('foto_bukti');

            $table->date('tanggal_perbaikan')
                ->nullable()
                ->after('foto_perbaikan');
        });
    }

    public function down(): void
    {
        Schema::table('inventaris_kerusakan', function (Blueprint $table) {
            $table->dropColumn([
                'foto_perbaikan',
                'tanggal_perbaikan',
            ]);
        });
    }
};
