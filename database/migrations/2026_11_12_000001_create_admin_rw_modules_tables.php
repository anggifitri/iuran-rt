<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Audit Logs Table
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_name')->nullable();
                $table->string('role')->nullable();
                $table->string('event'); // login, logout, create, update, delete, verify, approve, reject, revise, mutate
                $table->string('module'); // Warga, Verifikasi, Mutasi, Kas, Surat, Pengaduan, Ronda, Fasilitas, Laporan RT, dll
                $table->string('record_id')->nullable();
                $table->string('rt_number')->nullable();
                $table->text('description')->nullable();
                $table->longText('old_values')->nullable();
                $table->longText('new_values')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        // 2. Add columns to wargas table for verification and status
        if (Schema::hasTable('wargas')) {
            Schema::table('wargas', function (Blueprint $table) {
                if (!Schema::hasColumn('wargas', 'status_verifikasi')) {
                    $table->string('status_verifikasi')->default('terverifikasi');
                }
                if (!Schema::hasColumn('wargas', 'catatan_verifikasi')) {
                    $table->text('catatan_verifikasi')->nullable();
                }
                if (!Schema::hasColumn('wargas', 'dokumen_pendukung')) {
                    $table->string('dokumen_pendukung')->nullable();
                }
                if (!Schema::hasColumn('wargas', 'tanggal_verifikasi')) {
                    $table->dateTime('tanggal_verifikasi')->nullable();
                }
                if (!Schema::hasColumn('wargas', 'verifikator_id')) {
                    $table->unsignedBigInteger('verifikator_id')->nullable();
                }
                if (!Schema::hasColumn('wargas', 'status_warga')) {
                    $table->string('status_warga')->default('aktif'); // aktif, pindah, meninggal
                }
            });
        }

        // 3. Mutasi Wargas Table
        if (!Schema::hasTable('mutasi_wargas')) {
            Schema::create('mutasi_wargas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('warga_id')->nullable();
                $table->string('nama_warga');
                $table->string('nik')->nullable();
                $table->string('tipe'); // masuk, keluar
                $table->string('rt_asal')->nullable();
                $table->string('rt_tujuan')->nullable();
                $table->text('alamat_asal')->nullable();
                $table->text('alamat_tujuan')->nullable();
                $table->date('tanggal_mutasi');
                $table->text('alasan')->nullable();
                $table->string('dokumen_pendukung')->nullable();
                $table->string('status')->default('selesai'); // selesai, pending, dibatalkan
                $table->text('catatan')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 4. Jadwal Ronda Table
        if (!Schema::hasTable('jadwal_rondas')) {
            Schema::create('jadwal_rondas', function (Blueprint $table) {
                $table->id();
                $table->string('rt_number');
                $table->date('tanggal');
                $table->string('shift')->default('Malam (22:00 - 04:00 WIB)');
                $table->string('lokasi_pos')->default('Pos Ronda RT');
                $table->text('petugas'); // Daftar nama petugas / kontak
                $table->string('status')->default('terjadwal'); // terjadwal, berjalan, terlaksana, batal
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        // 5. Laporan Keamanan Table
        if (!Schema::hasTable('laporan_keamanans')) {
            Schema::create('laporan_keamanans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('rt_number');
                $table->string('judul');
                $table->string('kategori')->default('Ketertiban Lingkungan');
                $table->dateTime('waktu_kejadian');
                $table->string('lokasi');
                $table->text('kronologi');
                $table->string('dokumentasi')->nullable();
                $table->string('status')->default('menunggu'); // menunggu, diproses, selesai
                $table->text('tindak_lanjut')->nullable();
                $table->string('petugas_penanganan')->nullable();
                $table->timestamps();
            });
        }

        // 6. Fasilitas Umum Table
        if (!Schema::hasTable('fasilitas_umums')) {
            Schema::create('fasilitas_umums', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('kategori')->default('Balai Warga / Aula');
                $table->text('deskripsi')->nullable();
                $table->string('lokasi');
                $table->integer('kapasitas')->default(0);
                $table->string('status_operasional')->default('tersedia'); // tersedia, perbaikan, nonaktif
                $table->string('foto')->nullable();
                $table->text('syarat_ketentuan')->nullable();
                $table->timestamps();
            });
        }

        // 7. Peminjaman Fasilitas Table
        if (!Schema::hasTable('peminjaman_fasilitas')) {
            Schema::create('peminjaman_fasilitas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fasilitas_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('nama_peminjam');
                $table->string('rt_number');
                $table->string('nomor_kontak')->nullable();
                $table->text('tujuan');
                $table->dateTime('waktu_mulai');
                $table->dateTime('waktu_selesai');
                $table->string('status')->default('menunggu'); // menunggu, disetujui, ditolak, selesai, dibatalkan
                $table->text('catatan_pengelola')->nullable();
                $table->unsignedBigInteger('disetujui_oleh')->nullable();
                $table->timestamps();
            });
        }

        // 8. Agenda & Rapat RW Table
        if (!Schema::hasTable('agenda_rapats')) {
            Schema::create('agenda_rapats', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->text('deskripsi')->nullable();
                $table->dateTime('tanggal_waktu');
                $table->string('lokasi');
                $table->string('penyelenggara')->default('Pengurus RW 018');
                $table->string('target_peserta')->default('Semua RT');
                $table->text('agenda_pembahasan')->nullable();
                $table->longText('notulen')->nullable();
                $table->longText('hasil_keputusan')->nullable();
                $table->string('lampiran_dokumen')->nullable();
                $table->string('status')->default('akan_datang'); // akan_datang, berlangsung, selesai, dibatalkan
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 9. Add columns to pengumumans table if needed
        if (Schema::hasTable('pengumumans')) {
            Schema::table('pengumumans', function (Blueprint $table) {
                if (!Schema::hasColumn('pengumumans', 'kategori')) {
                    $table->string('kategori')->default('informasi_umum');
                }
                if (!Schema::hasColumn('pengumumans', 'target_rt')) {
                    $table->string('target_rt')->default('all');
                }
                if (!Schema::hasColumn('pengumumans', 'lampiran_file')) {
                    $table->string('lampiran_file')->nullable();
                }
                if (!Schema::hasColumn('pengumumans', 'status')) {
                    $table->string('status')->default('published');
                }
            });
        }

        // 10. Laporan Lingkungan Table
        if (!Schema::hasTable('laporan_lingkungans')) {
            Schema::create('laporan_lingkungans', function (Blueprint $table) {
                $table->id();
                $table->string('rt_number');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('periode_bulan');
                $table->string('periode_tahun');
                $table->string('kondisi_kebersihan')->default('Bersih & Rapi');
                $table->string('kondisi_drainase')->default('Lancar Tanpa Genangan');
                $table->text('program_penghijauan')->nullable();
                $table->text('kegiatan_kerja_bakti')->nullable();
                $table->text('kendala_lingkungan')->nullable();
                $table->string('foto_dokumentasi')->nullable();
                $table->text('catatan_rw')->nullable();
                $table->string('status')->default('tercatat');
                $table->timestamps();
            });
        }

        // 11. Bank Sampah Table
        if (!Schema::hasTable('bank_sampahs')) {
            Schema::create('bank_sampahs', function (Blueprint $table) {
                $table->id();
                $table->string('rt_number');
                $table->unsignedBigInteger('warga_id')->nullable();
                $table->string('nama_penyetor');
                $table->date('tanggal_setor');
                $table->string('kategori'); // plastik, kertas, kardus, logam, kaca, lainnya
                $table->decimal('berat_kg', 8, 2)->default(0);
                $table->decimal('nilai_rupiah', 12, 2)->default(0);
                $table->text('catatan')->nullable();
                $table->string('petugas')->nullable();
                $table->timestamps();
            });
        }

        // 12. Laporan Resmi RT & Pemeriksaan RW
        if (!Schema::hasTable('laporan_rts')) {
            Schema::create('laporan_rts', function (Blueprint $table) {
                $table->id();
                $table->string('rt_number');
                $table->string('bulan');
                $table->string('tahun');
                $table->unsignedBigInteger('pengirim_id')->nullable();
                $table->string('status')->default('draft'); // draft, menunggu, diterima, perlu_revisi
                $table->longText('snapshot_data')->nullable(); // JSON snapshot of demographics, finance, iuran, services
                $table->text('evaluasi_kegiatan')->nullable();
                $table->text('kendala')->nullable();
                $table->text('catatan_tambahan')->nullable();
                $table->text('catatan_pemeriksaan_rw')->nullable();
                $table->unsignedBigInteger('pemeriksa_id')->nullable();
                $table->dateTime('waktu_pemeriksaan')->nullable();
                $table->longText('riwayat_revisi')->nullable(); // JSON of revisions
                $table->integer('version')->default(1);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_rts');
        Schema::dropIfExists('bank_sampahs');
        Schema::dropIfExists('laporan_lingkungans');
        Schema::dropIfExists('agenda_rapats');
        Schema::dropIfExists('peminjaman_fasilitas');
        Schema::dropIfExists('fasilitas_umums');
        Schema::dropIfExists('laporan_keamanans');
        Schema::dropIfExists('jadwal_rondas');
        Schema::dropIfExists('mutasi_wargas');
        Schema::dropIfExists('audit_logs');
    }
};
