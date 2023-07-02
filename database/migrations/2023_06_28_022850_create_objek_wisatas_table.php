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
        Schema::create('objek_wisata', function (Blueprint $table) {
            $table->id("id");
            $table->string('nama')->unique();
            $table->text('deskripsi');
            $table->text('alamat_lengkap');
            $table->string('kab_kota', 100);
            $table->string('provinsi', 50);
            $table->string('fasilitas', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objek_wisata');
    }
};
