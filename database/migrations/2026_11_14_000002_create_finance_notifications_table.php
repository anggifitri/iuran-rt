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
        if (!Schema::hasTable('finance_notifications')) {
            Schema::create('finance_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->foreignId('warga_id')->nullable()->constrained('wargas')->onDelete('cascade');
                $table->string('tipe', 50); // tagihan_baru, jatuh_tempo, terlambat, pembayaran_pending, pembayaran_diterima, pembayaran_ditolak
                $table->string('judul');
                $table->text('pesan');
                $table->decimal('nominal', 12, 2)->nullable();
                $table->string('periode', 20)->nullable();
                $table->date('due_date')->nullable();
                $table->string('link_url')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->json('data')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'is_read']);
                $table->index('warga_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_notifications');
    }
};
