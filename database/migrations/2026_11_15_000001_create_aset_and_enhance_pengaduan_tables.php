<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabel Aset Lingkungan RW 018
        if (!Schema::hasTable('asets')) {
            Schema::create('asets', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_aset')->unique(); // e.g. TL-018-006-012
                $table->string('nama');
                $table->string('kategori'); // Tiang Listrik & Lampu Jalan, CCTV & Keamanan, Hydrant & Utilitas Air, Saluran Air & Drainase, Fasilitas Umum, Infrastruktur Lingkungan, Sanitasi & Bak Sampah
                $table->string('rt_number', 10)->default('006');
                $table->string('rw_number', 10)->default('018');
                $table->string('lokasi'); // Alamat / patokan lokasi
                $table->double('latitude')->nullable();
                $table->double('longitude')->nullable();
                $table->string('kondisi')->default('baik'); // baik, rusak_ringan, rusak_berat, dalam_perbaikan
                $table->string('status_pemeliharaan')->default('normal'); // normal, jadwal_servis, perlu_tindakan, sedang_dikerjakan
                $table->date('tanggal_perolehan')->nullable();
                $table->dateTime('terakhir_diperiksa')->nullable();
                $table->string('foto')->nullable();
                $table->text('spesifikasi')->nullable();
                $table->timestamps();
            });
        }

        // 2. Tambahkan kolom pendukung pada tabel pengaduans jika belum ada
        Schema::table('pengaduans', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaduans', 'nomor_pengaduan')) {
                $table->string('nomor_pengaduan')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('pengaduans', 'aset_id')) {
                $table->foreignId('aset_id')->nullable()->after('barcode_code')->constrained('asets')->nullOnDelete();
            }
            if (!Schema::hasColumn('pengaduans', 'priority')) {
                $table->string('priority')->default('sedang')->after('status'); // rendah, sedang, tinggi, darurat
            }
            if (!Schema::hasColumn('pengaduans', 'foto_lampiran')) {
                $table->string('foto_lampiran')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('pengaduans', 'petugas_nama')) {
                $table->string('petugas_nama')->nullable()->after('foto_lampiran');
            }
            if (!Schema::hasColumn('pengaduans', 'petugas_user_id')) {
                $table->foreignId('petugas_user_id')->nullable()->after('petugas_nama')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('pengaduans', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('petugas_user_id'); // Khusus RW & RT
            }
            if (!Schema::hasColumn('pengaduans', 'feedback_warga')) {
                $table->text('feedback_warga')->nullable()->after('internal_notes'); // Terlihat oleh pelapor
            }
            if (!Schema::hasColumn('pengaduans', 'verified_at')) {
                $table->dateTime('verified_at')->nullable()->after('feedback_warga');
            }
            if (!Schema::hasColumn('pengaduans', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('pengaduans', 'deadline_at')) {
                $table->dateTime('deadline_at')->nullable()->after('verified_by');
            }
            if (!Schema::hasColumn('pengaduans', 'completed_at')) {
                $table->dateTime('completed_at')->nullable()->after('deadline_at');
            }
        });

        // 3. Riwayat Pemeriksaan & Perawatan Aset
        if (!Schema::hasTable('aset_pemeriksaans')) {
            Schema::create('aset_pemeriksaans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('aset_id')->constrained('asets')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('pengaduan_id')->nullable()->constrained('pengaduans')->nullOnDelete();
                $table->string('jenis_kegiatan')->default('pemeriksaan_rutin'); // pemeriksaan_rutin, inspeksi_pengaduan, perawatan_berkala, perbaikan_kerusakan
                $table->dateTime('tanggal');
                $table->string('kondisi_sebelum')->nullable();
                $table->string('kondisi_setelah')->nullable();
                $table->text('catatan')->nullable();
                $table->string('foto_sebelum')->nullable();
                $table->string('foto_setelah')->nullable();
                $table->decimal('biaya', 14, 2)->default(0);
                $table->string('petugas_nama')->nullable();
                $table->timestamps();
            });
        }

        // 4. Riwayat Status & Log Pengaduan
        if (!Schema::hasTable('pengaduan_histories')) {
            Schema::create('pengaduan_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pengaduan_id')->constrained('pengaduans')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status_dari')->nullable();
                $table->string('status_ke');
                $table->text('catatan')->nullable();
                $table->boolean('is_internal')->default(false);
                $table->timestamps();
            });
        }

        // 5. Dokumentasi Foto & Identifikasi Aset
        if (!Schema::hasTable('aset_dokumentasis')) {
            Schema::create('aset_dokumentasis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('aset_id')->nullable()->constrained('asets')->nullOnDelete();
                $table->foreignId('pengaduan_id')->nullable()->constrained('pengaduans')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('foto');
                $table->string('tipe_dokumentasi')->default('kondisi_awal'); // kondisi_awal, kerusakan, proses_pemeriksaan, hasil_perbaikan
                $table->text('keterangan')->nullable();
                $table->double('latitude')->nullable();
                $table->double('longitude')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('aset_dokumentasis');
        Schema::dropIfExists('pengaduan_histories');
        Schema::dropIfExists('aset_pemeriksaans');
        Schema::dropIfExists('asets');
    }
};
