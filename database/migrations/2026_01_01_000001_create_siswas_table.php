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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 20)->unique();
            $table->string('nama', 150);
            $table->string('kelas', 50)->default('X-A');
            $table->string('email', 100)->nullable();
            $table->enum('gaya_belajar', ['Visual', 'Auditori', 'Kinestetik', 'Baca/Tulis'])->default('Visual');
            $table->enum('status', ['Aktif', 'Non-Aktif', 'Mutasi', 'Lulus'])->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
