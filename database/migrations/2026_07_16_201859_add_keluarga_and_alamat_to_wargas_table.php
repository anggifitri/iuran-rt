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
        Schema::table('wargas', function (Blueprint $table) {
            $table->boolean('is_kk')->default(true);
            $table->unsignedBigInteger('kk_id')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('rt_number')->nullable();
            $table->string('rw_number')->nullable();
            $table->text('alamat')->nullable();
            $table->foreign('kk_id')->references('id')->on('wargas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->dropForeign(['kk_id']);
            $table->dropColumn(['is_kk', 'kk_id', 'tanggal_lahir', 'rt_number', 'rw_number', 'alamat']);
        });
    }
};
