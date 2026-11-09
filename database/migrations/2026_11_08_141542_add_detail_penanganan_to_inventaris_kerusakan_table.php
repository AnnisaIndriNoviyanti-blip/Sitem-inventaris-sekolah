
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventaris_kerusakan', function (Blueprint $table) {
            $table->text('tindakan_perbaikan')
                ->nullable()
                ->after('foto_perbaikan');

            $table->text('hasil_perbaikan')
                ->nullable()
                ->after('tindakan_perbaikan');
        });
    }

    public function down(): void
    {
        Schema::table('inventaris_kerusakan', function (Blueprint $table) {
            $table->dropColumn([
                'tindakan_perbaikan',
                'hasil_perbaikan',
            ]);
        });
    }
};
