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
        Schema::create('kredensial_admins', function (Blueprint $table) {
            $table->id('id');
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['male', 'female']);
            $table->text('alamat');
            $table->string('nik', 16);
            $table->string('no_telepon', 16);
            $table->foreignId('id_user');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kredensial_admins');
    }
};
