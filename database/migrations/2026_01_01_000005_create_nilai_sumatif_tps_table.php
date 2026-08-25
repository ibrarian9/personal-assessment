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
        Schema::create('nilai_sumatif_tps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('tujuan_pembelajaran_id')->constrained('tujuan_pembelajarans')->onDelete('cascade');
            $table->decimal('nilai', 5, 2)->default(0.00); // 0 - 100
            $table->enum('status_ketuntasan', ['Tuntas', 'Belum Tuntas'])->default('Belum Tuntas');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'tujuan_pembelajaran_id'], 'unique_siswa_tp_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_sumatif_tps');
    }
};
