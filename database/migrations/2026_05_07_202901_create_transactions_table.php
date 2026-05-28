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
       Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        // Menghubungkan transaksi ke warga (Foreign Key)
        $table->foreignId('warga_id')->nullable()->constrained('wargas')->onDelete('set null');

        $table->enum('tipe', ['masuk', 'keluar']); // Pilihan kaku: masuk atau keluar
        $table->bigInteger('jumlah'); // Nominal uang
        $table->string('kategori'); // Contoh: Iuran Bulanan, Listrik, Sampah
        $table->text('keterangan')->nullable();
        $table->date('tanggal'); // Tanggal bayar/pengeluaran
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
