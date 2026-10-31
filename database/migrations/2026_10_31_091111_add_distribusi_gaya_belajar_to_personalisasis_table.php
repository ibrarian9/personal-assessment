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
            $table->json('distribusi_gaya_belajar')->nullable()->after('gaya_belajar_rekomendasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personalisasis', function (Blueprint $table) {
            $table->dropColumn('distribusi_gaya_belajar');
        });
    }
};
