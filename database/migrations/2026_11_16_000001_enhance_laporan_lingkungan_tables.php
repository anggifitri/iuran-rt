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
        // 1. Tambah kolom lengkap pada tabel laporan_lingkungans
        Schema::table('laporan_lingkungans', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_lingkungans', 'tanggal_pengiriman')) {
                $table->timestamp('tanggal_pengiriman')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'area_terjaga_kebersihan')) {
                $table->text('area_terjaga_kebersihan')->nullable()->after('kondisi_kebersihan');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'area_perlu_perhatian_kebersihan')) {
                $table->text('area_perlu_perhatian_kebersihan')->nullable()->after('area_terjaga_kebersihan');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'masalah_sampah')) {
                $table->text('masalah_sampah')->nullable()->after('area_perlu_perhatian_kebersihan');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'keterangan_kebersihan')) {
                $table->text('keterangan_kebersihan')->nullable()->after('masalah_sampah');
            }

            if (!Schema::hasColumn('laporan_lingkungans', 'kondisi_aliran_air')) {
                $table->string('kondisi_aliran_air')->nullable()->after('kondisi_drainase');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'titik_rawan_genangan')) {
                $table->text('titik_rawan_genangan')->nullable()->after('kondisi_aliran_air');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'saluran_butuh_pembersihan')) {
                $table->text('saluran_butuh_pembersihan')->nullable()->after('titik_rawan_genangan');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'keterangan_drainase')) {
                $table->text('keterangan_drainase')->nullable()->after('saluran_butuh_pembersihan');
            }

            if (!Schema::hasColumn('laporan_lingkungans', 'kondisi_penghijauan')) {
                $table->string('kondisi_penghijauan')->nullable()->after('program_penghijauan');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'ketersediaan_ruang_terbuka')) {
                $table->text('ketersediaan_ruang_terbuka')->nullable()->after('kondisi_penghijauan');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'kegiatan_perawatan_tanaman')) {
                $table->text('kegiatan_perawatan_tanaman')->nullable()->after('ketersediaan_ruang_terbuka');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'kendala_penghijauan')) {
                $table->text('kendala_penghijauan')->nullable()->after('kegiatan_perawatan_tanaman');
            }

            if (!Schema::hasColumn('laporan_lingkungans', 'program_kebersihan')) {
                $table->text('program_kebersihan')->nullable()->after('kegiatan_kerja_bakti');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'program_lingkungan_lainnya')) {
                $table->text('program_lingkungan_lainnya')->nullable()->after('program_kebersihan');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'tanggal_pelaksanaan_kegiatan')) {
                $table->date('tanggal_pelaksanaan_kegiatan')->nullable()->after('program_lingkungan_lainnya');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'jumlah_peserta_kegiatan')) {
                $table->integer('jumlah_peserta_kegiatan')->default(0)->after('tanggal_pelaksanaan_kegiatan');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'agenda_id')) {
                $table->unsignedBigInteger('agenda_id')->nullable()->after('jumlah_peserta_kegiatan');
            }

            if (!Schema::hasColumn('laporan_lingkungans', 'lokasi_perhatian_khusus')) {
                $table->text('lokasi_perhatian_khusus')->nullable()->after('kendala_lingkungan');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'rencana_tindak_lanjut')) {
                $table->text('rencana_tindak_lanjut')->nullable()->after('lokasi_perhatian_khusus');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'bantuan_rw_dibutuhkan')) {
                $table->text('bantuan_rw_dibutuhkan')->nullable()->after('rencana_tindak_lanjut');
            }

            if (!Schema::hasColumn('laporan_lingkungans', 'foto_sebelum')) {
                $table->string('foto_sebelum')->nullable()->after('foto_dokumentasi');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'foto_sesudah')) {
                $table->string('foto_sesudah')->nullable()->after('foto_sebelum');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'keterangan_foto')) {
                $table->text('keterangan_foto')->nullable()->after('foto_sesudah');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'dokumentasi_tambahan')) {
                $table->longText('dokumentasi_tambahan')->nullable()->after('keterangan_foto');
            }

            if (!Schema::hasColumn('laporan_lingkungans', 'pemeriksa_id')) {
                $table->unsignedBigInteger('pemeriksa_id')->nullable()->after('catatan_rw');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'tanggal_pemeriksaan')) {
                $table->timestamp('tanggal_pemeriksaan')->nullable()->after('pemeriksa_id');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'catatan_evaluasi_rw')) {
                $table->text('catatan_evaluasi_rw')->nullable()->after('tanggal_pemeriksaan');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'catatan_revisi_rt')) {
                $table->text('catatan_revisi_rt')->nullable()->after('catatan_evaluasi_rw');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'versi')) {
                $table->integer('versi')->default(1)->after('catatan_revisi_rt');
            }
            if (!Schema::hasColumn('laporan_lingkungans', 'is_terlambat')) {
                $table->boolean('is_terlambat')->default(false)->after('versi');
            }
        });

        // 2. Buat tabel laporan_lingkungan_histories untuk log audit & versi revisi
        if (!Schema::hasTable('laporan_lingkungan_histories')) {
            Schema::create('laporan_lingkungan_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('laporan_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('aksi'); // draft_disimpan, dikirim_ke_rw, revisi_diminta, revisi_dikirim, diterima
                $table->string('status_sebelum')->nullable();
                $table->string('status_sesudah');
                $table->text('catatan')->nullable();
                $table->integer('versi')->default(1);
                $table->longText('snapshot_data')->nullable(); // JSON snapshot perubahan
                $table->timestamps();

                $table->foreign('laporan_id')->references('id')->on('laporan_lingkungans')->onDelete('cascade');
            });
        }

        // 3. Buat tabel laporan_lingkungan_periodes untuk batas waktu & instruksi RW
        if (!Schema::hasTable('laporan_lingkungan_periodes')) {
            Schema::create('laporan_lingkungan_periodes', function (Blueprint $table) {
                $table->id();
                $table->string('periode_bulan');
                $table->string('periode_tahun');
                $table->date('tanggal_mulai');
                $table->date('batas_waktu');
                $table->string('status')->default('aktif'); // aktif, ditutup
                $table->text('catatan_instruksi_rw')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_lingkungan_histories');
        Schema::dropIfExists('laporan_lingkungan_periodes');
    }
};
