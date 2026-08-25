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
        Schema::create('tujuan_pembelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lingkup_materi_id')->constrained('lingkup_materis')->onDelete('cascade');
            $table->string('kode_tp', 50); // e.g. "TP 1.1", "TP 1.2"
            $table->text('deskripsi_tp'); // e.g. "Mengidentifikasi organ utama penyusun sistem gerak manusia..."
            $table->decimal('target_ketercapaian', 5, 2)->default(75.00);
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tujuan_pembelajarans');
    }
};
