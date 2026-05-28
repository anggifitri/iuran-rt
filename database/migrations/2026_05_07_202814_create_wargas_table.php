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
        // Ubah jadi 'wargas' biar sinkron dengan Model Warga
        Schema::create('wargas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('blok_rumah');
            $table->string('nomor_hp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wargas');
    }
};
