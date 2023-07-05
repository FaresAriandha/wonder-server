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
        Schema::create('komentar_objek_wisata', function (Blueprint $table) {
            $table->id("id");
            $table->foreignId("id_user");
            $table->foreignId("id_objek_wisata");
            $table->text("komentar");
            $table->timestamps();

            $table->foreign("id_user", "fk_komentar_user")->references("id")->on("users")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign("id_objek_wisata", "fk_komentar_wisata")->references("id")->on("objek_wisata")->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('komentar_objek_wisata');
    }
};
