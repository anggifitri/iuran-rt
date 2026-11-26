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
        // 1. Enhance fasilitas_umums table
        Schema::table('fasilitas_umums', function (Blueprint $table) {
            if (!Schema::hasColumn('fasilitas_umums', 'kondisi_fisik')) {
                $table->string('kondisi_fisik')->default('Baik')->after('status_operasional'); // Baik, Perlu Perawatan, Tidak Tersedia
            }
            if (!Schema::hasColumn('fasilitas_umums', 'daftar_perlengkapan')) {
                $table->text('daftar_perlengkapan')->nullable()->after('kondisi_fisik');
            }
            if (!Schema::hasColumn('fasilitas_umums', 'jam_operasional')) {
                $table->string('jam_operasional')->default('08:00 - 22:00 WIB')->after('daftar_perlengkapan');
            }
            if (!Schema::hasColumn('fasilitas_umums', 'aturan_penggunaan')) {
                $table->text('aturan_penggunaan')->nullable()->after('jam_operasional');
            }
            if (!Schema::hasColumn('fasilitas_umums', 'biaya_sewa')) {
                $table->decimal('biaya_sewa', 12, 2)->default(0)->after('aturan_penggunaan');
            }
            if (!Schema::hasColumn('fasilitas_umums', 'uang_jaminan')) {
                $table->decimal('uang_jaminan', 12, 2)->default(0)->after('biaya_sewa');
            }
            if (!Schema::hasColumn('fasilitas_umums', 'penanggung_jawab')) {
                $table->string('penanggung_jawab')->nullable()->after('uang_jaminan');
            }
            if (!Schema::hasColumn('fasilitas_umums', 'kontak_pj')) {
                $table->string('kontak_pj')->nullable()->after('penanggung_jawab');
            }
        });

        // 2. Enhance peminjaman_fasilitas table
        Schema::table('peminjaman_fasilitas', function (Blueprint $table) {
            if (!Schema::hasColumn('peminjaman_fasilitas', 'nomor_pengajuan')) {
                $table->string('nomor_pengajuan')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('peminjaman_fasilitas', 'nik_peminjam')) {
                $table->string('nik_peminjam')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('peminjaman_fasilitas', 'jumlah_peserta')) {
                $table->integer('jumlah_peserta')->default(1)->after('tujuan');
            }
            if (!Schema::hasColumn('peminjaman_fasilitas', 'dokumen_pendukung')) {
                $table->string('dokumen_pendukung')->nullable()->after('catatan_pengelola');
            }
            if (!Schema::hasColumn('peminjaman_fasilitas', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')->nullable()->after('dokumen_pendukung');
            }
            if (!Schema::hasColumn('peminjaman_fasilitas', 'biaya_sewa')) {
                $table->decimal('biaya_sewa', 12, 2)->default(0)->after('alasan_penolakan');
            }
            if (!Schema::hasColumn('peminjaman_fasilitas', 'uang_jaminan')) {
                $table->decimal('uang_jaminan', 12, 2)->default(0)->after('biaya_sewa');
            }
            if (!Schema::hasColumn('peminjaman_fasilitas', 'status_pembayaran')) {
                $table->string('status_pembayaran')->default('gratis')->after('uang_jaminan'); // gratis, menunggu_pembayaran, lunas
            }
        });

        // 3. Create pemeriksaan_fasilitas table (Pemeriksaan Kondisi Sebelum & Sesudah)
        if (!Schema::hasTable('pemeriksaan_fasilitas')) {
            Schema::create('pemeriksaan_fasilitas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('peminjaman_id');
                $table->unsignedBigInteger('fasilitas_id');
                $table->string('petugas_nama');
                $table->dateTime('tanggal_cek_awal')->nullable();
                $table->string('kondisi_sebelum')->default('Baik'); // Baik, Cukup, Ada Kerusakan Minor
                $table->text('catatan_sebelum')->nullable();
                $table->string('foto_sebelum')->nullable();
                $table->text('perlengkapan_keluar')->nullable();
                $table->dateTime('tanggal_cek_akhir')->nullable();
                $table->string('kondisi_sesudah')->nullable(); // Baik, Kotor, Ada Kerusakan, Ada Kehilangan
                $table->text('catatan_sesudah')->nullable();
                $table->string('foto_sesudah')->nullable();
                $table->text('perlengkapan_kembali')->nullable();
                $table->boolean('ada_kerusakan')->default(false);
                $table->text('tindak_lanjut_kerusakan')->nullable();
                $table->timestamps();

                $table->foreign('peminjaman_id')->references('id')->on('peminjaman_fasilitas')->onDelete('cascade');
                $table->foreign('fasilitas_id')->references('id')->on('fasilitas_umums')->onDelete('cascade');
            });
        }

        // 4. Create peminjaman_histories table (Status Change Timeline)
        if (!Schema::hasTable('peminjaman_histories')) {
            Schema::create('peminjaman_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('peminjaman_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('status_sebelumnya')->nullable();
                $table->string('status_baru');
                $table->text('keterangan')->nullable();
                $table->string('petugas')->nullable();
                $table->timestamps();

                $table->foreign('peminjaman_id')->references('id')->on('peminjaman_fasilitas')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_histories');
        Schema::dropIfExists('pemeriksaan_fasilitas');

        Schema::table('peminjaman_fasilitas', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_pengajuan',
                'nik_peminjam',
                'jumlah_peserta',
                'dokumen_pendukung',
                'alasan_penolakan',
                'biaya_sewa',
                'uang_jaminan',
                'status_pembayaran',
            ]);
        });

        Schema::table('fasilitas_umums', function (Blueprint $table) {
            $table->dropColumn([
                'kondisi_fisik',
                'daftar_perlengkapan',
                'jam_operasional',
                'aturan_penggunaan',
                'biaya_sewa',
                'uang_jaminan',
                'penanggung_jawab',
                'kontak_pj',
            ]);
        });
    }
};
