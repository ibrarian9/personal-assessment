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
        Schema::table('personalisasis', function (Blueprint $table) {
            $table->string('sumber_analisis')->nullable()->after('kategori_pemahaman');
            $table->string('keterangan_sumber')->nullable()->after('sumber_analisis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personalisasis', function (Blueprint $table) {
            $table->dropColumn(['sumber_analisis', 'keterangan_sumber']);
        });
    }
};
