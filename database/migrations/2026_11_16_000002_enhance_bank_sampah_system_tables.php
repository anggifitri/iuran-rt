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
        // 1. Tambah kolom lengkap pada tabel bank_sampahs
        Schema::table('bank_sampahs', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_sampahs', 'nomor_transaksi')) {
                $table->string('nomor_transaksi')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('bank_sampahs', 'satuan')) {
                $table->string('satuan')->default('Kg')->after('berat_kg');
            }
            if (!Schema::hasColumn('bank_sampahs', 'harga_per_satuan')) {
                $table->decimal('harga_per_satuan', 12, 2)->default(0)->after('satuan');
            }
            if (!Schema::hasColumn('bank_sampahs', 'status')) {
                $table->string('status')->default('diterima')->after('nilai_rupiah'); // menunggu, diterima, ditolak
            }
            if (!Schema::hasColumn('bank_sampahs', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')->nullable()->after('status');
            }
            if (!Schema::hasColumn('bank_sampahs', 'foto_dokumentasi')) {
                $table->string('foto_dokumentasi')->nullable()->after('alasan_penolakan');
            }
            if (!Schema::hasColumn('bank_sampahs', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('petugas');
            }
            if (!Schema::hasColumn('bank_sampahs', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }
            if (!Schema::hasColumn('bank_sampahs', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('warga_id');
            }
        });

        // 2. Buat tabel bank_sampah_kategoris
        if (!Schema::hasTable('bank_sampah_kategoris')) {
            Schema::create('bank_sampah_kategoris', function (Blueprint $table) {
                $table->id();
                $table->string('kode')->unique();
                $table->string('nama_kategori');
                $table->text('deskripsi')->nullable();
                $table->string('satuan')->default('Kg');
                $table->decimal('harga_min', 12, 2)->default(0);
                $table->decimal('harga_max', 12, 2)->default(0);
                $table->decimal('harga_acuan', 12, 2)->default(0);
                $table->text('tips_pemilahan')->nullable();
                $table->string('icon')->default('fa-recycle');
                $table->string('accent_color')->default('#285943');
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 3. Buat tabel bank_sampah_harga_histories
        if (!Schema::hasTable('bank_sampah_harga_histories')) {
            Schema::create('bank_sampah_harga_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('kategori_id');
                $table->decimal('harga_lama', 12, 2);
                $table->decimal('harga_baru', 12, 2);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('catatan')->nullable();
                $table->timestamps();

                $table->foreign('kategori_id')->references('id')->on('bank_sampah_kategoris')->onDelete('cascade');
            });
        }

        // 4. Buat tabel bank_sampah_penarikans (Pencatatan Penarikan Saldo Tabungan)
        if (!Schema::hasTable('bank_sampah_penarikans')) {
            Schema::create('bank_sampah_penarikans', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_penarikan')->unique();
                $table->unsignedBigInteger('warga_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('rt_number');
                $table->date('tanggal_penarikan');
                $table->decimal('jumlah_penarikan', 12, 2);
                $table->string('metode_penarikan')->default('tunai'); // tunai, transfer, potong_iuran
                $table->string('petugas');
                $table->text('catatan')->nullable();
                $table->string('status')->default('selesai'); // selesai, dibatalkan
                $table->timestamps();

                $table->foreign('warga_id')->references('id')->on('wargas')->onDelete('cascade');
            });
        }

        // 5. Buat tabel bank_sampah_edukasis (Artikel / Panduan Edukasi Lingkungan)
        if (!Schema::hasTable('bank_sampah_edukasis')) {
            Schema::create('bank_sampah_edukasis', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->string('kategori_materi'); // Pemilahan, Daur Ulang, Kompos, Zero Waste, Kebersihan
                $table->text('deskripsi_singkat');
                $table->longText('isi_materi');
                $table->string('icon')->default('fa-book-open');
                $table->string('gambar_ilustrasi')->nullable();
                $table->string('status')->default('published'); // published, draft, archived
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_sampah_edukasis');
        Schema::dropIfExists('bank_sampah_penarikans');
        Schema::dropIfExists('bank_sampah_harga_histories');
        Schema::dropIfExists('bank_sampah_kategoris');
    }
};
