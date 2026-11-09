
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('password_encrypted')->nullable();
            $table->timestamp('password_rotated_at')->nullable();
            $table->unsignedSmallInteger('password_rotation_year')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'password_encrypted',
                'password_rotated_at',
                'password_rotation_year'
            ]);
        });
    }
};
