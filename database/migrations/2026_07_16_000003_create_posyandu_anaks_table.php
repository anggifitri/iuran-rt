<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posyandu_anaks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_anak');
            $table->integer('umur_bulan');
            $table->decimal('berat_badan', 8, 2);
            $table->decimal('tinggi_badan', 8, 2);
            $table->string('status_tumbuh')->default('Normal');
            $table->text('solusi')->nullable();
            $table->json('imunisasi_checked')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posyandu_anaks');
    }
};
