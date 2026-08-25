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
        Schema::create('personalisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->nullable()->constrained('mata_pelajarans')->onDelete('set null');
            $table->decimal('skor_performa', 5, 2)->default(0);
            $table->enum('kategori_pemahaman', ['Tinggi', 'Sedang', 'Perlu Pendampingan'])->default('Sedang');
            $table->string('gaya_belajar_rekomendasi')->nullable();
            $table->integer('tp_tuntas_count')->default(0);
            $table->integer('tp_belum_tuntas_count')->default(0);
            $table->text('daftar_tp_remedial')->nullable(); // JSON / teks ringkas TP yang butuh remedial
            $table->text('rekomendasi_materi')->nullable();
            $table->text('rekomendasi_aktivitas')->nullable();
            $table->text('catatan_guru')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personalisasis');
    }
};
