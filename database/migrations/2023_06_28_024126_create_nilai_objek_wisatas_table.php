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
        Schema::create('nilai_objek_wisata', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('id_objek_wisata');
            $table->foreignId('id_kriteria');
            $table->string('nilai');
            $table->timestamps();

            // $table->foreign('id_objek_wisata')->references('objek_wisata')->on('id')->cascadeOnDelete()->cascadeOnUpdate();
            // $table->foreign('id_kriteria')->references('objek_wisata')->on('id')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_objek_wisata');
    }
};
