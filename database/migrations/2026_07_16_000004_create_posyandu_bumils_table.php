<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posyandu_bumils', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ibu');
            $table->integer('usia_kehamilan_minggu');
            $table->decimal('berat_badan', 8, 2);
            $table->string('tekanan_darah');
            $table->decimal('lila', 8, 2);
            $table->string('status_kesehatan')->default('Normal');
            $table->text('solusi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posyandu_bumils');
    }
};
