<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance posyandus table (Jadwal Kegiatan)
        Schema::table('posyandus', function (Blueprint $table) {
            if (!Schema::hasColumn('posyandus', 'status')) {
                $table->string('status')->default('terjadwal')->after('keterangan'); // terjadwal, selesai, dibatalkan
            }
            if (!Schema::hasColumn('posyandus', 'kategori')) {
                $table->string('kategori')->default('Balita')->after('status'); // Balita, Ibu Hamil, Lansia, Terpadu
            }
            if (!Schema::hasColumn('posyandus', 'jam_mulai')) {
                $table->string('jam_mulai')->nullable()->after('kategori');
            }
            if (!Schema::hasColumn('posyandus', 'jam_selesai')) {
                $table->string('jam_selesai')->nullable()->after('jam_mulai');
            }
            if (!Schema::hasColumn('posyandus', 'penanggung_jawab')) {
                $table->string('penanggung_jawab')->nullable()->after('jam_selesai');
            }
        });

        // 2. Enhance posyandu_anaks table (Profil Balita)
        Schema::table('posyandu_anaks', function (Blueprint $table) {
            if (!Schema::hasColumn('posyandu_anaks', 'warga_id')) {
                $table->foreignId('warga_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'nik')) {
                $table->string('nik')->nullable()->after('nama_anak');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'jenis_kelamin')) {
                $table->string('jenis_kelamin', 2)->default('L')->after('nik'); // L or P
            }
            if (!Schema::hasColumn('posyandu_anaks', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('jenis_kelamin');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'nama_ibu')) {
                $table->string('nama_ibu')->nullable()->after('tanggal_lahir');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'nama_ayah')) {
                $table->string('nama_ayah')->nullable()->after('nama_ibu');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'no_telepon')) {
                $table->string('no_telepon')->nullable()->after('nama_ayah');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'rt_number')) {
                $table->string('rt_number')->nullable()->after('no_telepon');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'alamat')) {
                $table->string('alamat')->nullable()->after('rt_number');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'berat_lahir')) {
                $table->decimal('berat_lahir', 8, 2)->nullable()->after('alamat');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'tinggi_lahir')) {
                $table->decimal('tinggi_lahir', 8, 2)->nullable()->after('berat_lahir');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'lingkar_kepala_lahir')) {
                $table->decimal('lingkar_kepala_lahir', 8, 2)->nullable()->after('tinggi_lahir');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'golongan_darah')) {
                $table->string('golongan_darah', 5)->nullable()->after('lingkar_kepala_lahir');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'catatan_khusus')) {
                $table->text('catatan_khusus')->nullable()->after('golongan_darah');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'perlu_tindak_lanjut')) {
                $table->boolean('perlu_tindak_lanjut')->default(false)->after('catatan_khusus');
            }
            if (!Schema::hasColumn('posyandu_anaks', 'alasan_tindak_lanjut')) {
                $table->string('alasan_tindak_lanjut')->nullable()->after('perlu_tindak_lanjut');
            }
        });

        // 3. Create posyandu_anak_pemeriksaans (Riwayat Pemeriksaan Balita)
        if (!Schema::hasTable('posyandu_anak_pemeriksaans')) {
            Schema::create('posyandu_anak_pemeriksaans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('posyandu_anak_id')->constrained('posyandu_anaks')->onDelete('cascade');
                $table->foreignId('posyandu_id')->nullable()->constrained('posyandus')->onDelete('set null');
                $table->date('tanggal_pemeriksaan');
                $table->decimal('umur_bulan', 8, 2);
                $table->decimal('berat_badan', 8, 2);
                $table->decimal('tinggi_badan', 8, 2);
                $table->decimal('lingkar_kepala', 8, 2)->nullable();
                $table->decimal('lingkar_lengan', 8, 2)->nullable();
                $table->decimal('imt', 8, 2)->nullable();
                $table->string('status_bb_u')->default('Gizi Baik'); // Gizi Buruk, Gizi Kurang, Gizi Baik, Berisiko Gizi Lebih
                $table->string('status_tb_u')->default('Normal'); // Sangat Pendek, Pendek, Normal, Tinggi
                $table->string('status_bb_tb')->default('Gizi Baik'); // Gizi Buruk, Gizi Kurang, Gizi Baik, Berisiko Gizi Lebih, Gizi Lebih, Obesitas
                $table->string('status_imt_u')->default('Gizi Baik'); // Gizi Buruk, Gizi Kurang, Gizi Baik, Berisiko Gizi Lebih, Gizi Lebih, Obesitas
                $table->string('status_tumbuh')->default('Normal');
                $table->string('asi_eksklusif')->nullable(); // Ya, Tidak
                $table->boolean('vitamin_a')->default(false);
                $table->boolean('obat_cacing')->default(false);
                $table->text('catatan_petugas')->nullable();
                $table->string('petugas_nama')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // 4. Create posyandu_anak_imunisasis (Pencatatan Imunisasi Balita)
        if (!Schema::hasTable('posyandu_anak_imunisasis')) {
            Schema::create('posyandu_anak_imunisasis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('posyandu_anak_id')->constrained('posyandu_anaks')->onDelete('cascade');
                $table->string('vaksin_nama');
                $table->date('tanggal_pemberian')->nullable();
                $table->string('status')->default('Terjadwal'); // Sudah Diberikan, Terjadwal, Terlewat
                $table->date('jadwal_berikutnya')->nullable();
                $table->string('batch_nomor')->nullable();
                $table->string('petugas_nama')->nullable();
                $table->text('catatan_petugas')->nullable();
                $table->timestamps();
            });
        }

        // 5. Enhance posyandu_bumils table (Profil Ibu Hamil)
        Schema::table('posyandu_bumils', function (Blueprint $table) {
            if (!Schema::hasColumn('posyandu_bumils', 'warga_id')) {
                $table->foreignId('warga_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('posyandu_bumils', 'nik')) {
                $table->string('nik')->nullable()->after('nama_ibu');
            }
            if (!Schema::hasColumn('posyandu_bumils', 'nama_suami')) {
                $table->string('nama_suami')->nullable()->after('nik');
            }
            if (!Schema::hasColumn('posyandu_bumils', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('nama_suami');
            }
            if (!Schema::hasColumn('posyandu_bumils', 'hpht')) {
                $table->date('hpht')->nullable()->after('tanggal_lahir');
            }
            if (!Schema::hasColumn('posyandu_bumils', 'hpl')) {
                $table->date('hpl')->nullable()->after('hpht');
            }
            if (!Schema::hasColumn('posyandu_bumils', 'hamil_ke')) {
                $table->integer('hamil_ke')->default(1)->after('hpl');
            }
            if (!Schema::hasColumn('posyandu_bumils', 'golongan_darah')) {
                $table->string('golongan_darah', 5)->nullable()->after('hamil_ke');
            }
            if (!Schema::hasColumn('posyandu_bumils', 'no_telepon')) {
                $table->string('no_telepon')->nullable()->after('golongan_darah');
            }
            if (!Schema::hasColumn('posyandu_bumils', 'rt_number')) {
                $table->string('rt_number')->nullable()->after('no_telepon');
            }
            if (!Schema::hasColumn('posyandu_bumils', 'alamat')) {
                $table->string('alamat')->nullable()->after('rt_number');
            }
            if (!Schema::hasColumn('posyandu_bumils', 'status_kehamilan')) {
                $table->string('status_kehamilan')->default('Aktif Hamil')->after('alamat'); // Aktif Hamil, Sudah Melahirkan, Gugur
            }
            if (!Schema::hasColumn('posyandu_bumils', 'perlu_tindak_lanjut')) {
                $table->boolean('perlu_tindak_lanjut')->default(false)->after('status_kehamilan');
            }
            if (!Schema::hasColumn('posyandu_bumils', 'alasan_tindak_lanjut')) {
                $table->string('alasan_tindak_lanjut')->nullable()->after('perlu_tindak_lanjut');
            }
        });

        // 6. Create posyandu_bumil_pemeriksaans (Riwayat ANC Ibu Hamil)
        if (!Schema::hasTable('posyandu_bumil_pemeriksaans')) {
            Schema::create('posyandu_bumil_pemeriksaans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('posyandu_bumil_id')->constrained('posyandu_bumils')->onDelete('cascade');
                $table->foreignId('posyandu_id')->nullable()->constrained('posyandus')->onDelete('set null');
                $table->date('tanggal_pemeriksaan');
                $table->integer('usia_kehamilan_minggu');
                $table->decimal('berat_badan', 8, 2);
                $table->string('tekanan_darah');
                $table->decimal('lila', 8, 2);
                $table->decimal('tinggi_fundus', 8, 2)->nullable();
                $table->string('detak_jantung_janin')->nullable();
                $table->text('keluhan')->nullable();
                $table->integer('pemberian_ttd')->default(0); // Jumlah tablet tambah darah
                $table->string('status_kesehatan')->default('Normal'); // Normal, Risiko KEK, Hipertensi Gestasional, Anemia Ringan, dll.
                $table->text('catatan_petugas')->nullable();
                $table->date('jadwal_berikutnya')->nullable();
                $table->string('petugas_nama')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('posyandu_bumil_pemeriksaans');
        Schema::dropIfExists('posyandu_anak_imunisasis');
        Schema::dropIfExists('posyandu_anak_pemeriksaans');
    }
};
