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
        // 1. Enhance jadwal_rondas
        Schema::table('jadwal_rondas', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_rondas', 'koordinator')) {
                $table->string('koordinator')->nullable()->after('petugas');
            }
            if (!Schema::hasColumn('jadwal_rondas', 'kontak_koordinator')) {
                $table->string('kontak_koordinator')->nullable()->after('koordinator');
            }
            if (!Schema::hasColumn('jadwal_rondas', 'laporan_patroli')) {
                $table->text('laporan_patroli')->nullable()->after('catatan');
            }
            if (!Schema::hasColumn('jadwal_rondas', 'dokumentasi_patroli')) {
                $table->string('dokumentasi_patroli')->nullable()->after('laporan_patroli');
            }
        });

        // 2. Enhance laporan_keamanans
        Schema::table('laporan_keamanans', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_keamanans', 'nomor_laporan')) {
                $table->string('nomor_laporan')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('laporan_keamanans', 'nama_pelapor')) {
                $table->string('nama_pelapor')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('laporan_keamanans', 'kontak_pelapor')) {
                $table->string('kontak_pelapor')->nullable()->after('nama_pelapor');
            }
            if (!Schema::hasColumn('laporan_keamanans', 'cctv_id')) {
                $table->unsignedBigInteger('cctv_id')->nullable()->after('lokasi');
            }
            if (!Schema::hasColumn('laporan_keamanans', 'dokumentasi_penanganan')) {
                $table->string('dokumentasi_penanganan')->nullable()->after('dokumentasi');
            }
            if (!Schema::hasColumn('laporan_keamanans', 'selesai_at')) {
                $table->dateTime('selesai_at')->nullable()->after('petugas_penanganan');
            }
        });

        // 3. Create cctvs table
        if (!Schema::hasTable('cctvs')) {
            Schema::create('cctvs', function (Blueprint $table) {
                $table->id();
                $table->string('kode_kamera')->unique();
                $table->string('nama_kamera');
                $table->string('rt_number')->default('RW'); // 006, 007, 008, 009, 010, RW
                $table->string('lokasi');
                $table->text('area_pantau');
                $table->string('status')->default('online'); // online, offline, gangguan
                $table->text('catatan_gangguan')->nullable();
                $table->date('tanggal_pemasangan')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('stream_url')->nullable();
                $table->string('tipe_kamera')->nullable(); // e.g. PTZ Dome 4MP 360, Bullet Fixed 2MP IR
                $table->string('kapasitas_penyimpanan')->nullable(); // e.g. 2 TB (Retensi 30 Hari)
                $table->string('maps_url')->nullable();
                $table->timestamp('last_online_at')->nullable();
                $table->timestamps();
            });
        }

        // 4. Create cctv_recordings table
        if (!Schema::hasTable('cctv_recordings')) {
            Schema::create('cctv_recordings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cctv_id');
                $table->unsignedBigInteger('laporan_keamanan_id')->nullable();
                $table->string('judul_rekaman');
                $table->dateTime('waktu_mulai');
                $table->dateTime('waktu_selesai');
                $table->string('ukuran_file')->default('500 MB');
                $table->string('file_path')->nullable();
                $table->boolean('is_locked')->default(false); // evidence locked from auto purge
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->foreign('cctv_id')->references('id')->on('cctvs')->onDelete('cascade');
            });
        }

        // 5. Create laporan_keamanan_histories table
        if (!Schema::hasTable('laporan_keamanan_histories')) {
            Schema::create('laporan_keamanan_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('laporan_keamanan_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('status_sebelumnya')->nullable();
                $table->string('status_baru');
                $table->text('keterangan')->nullable();
                $table->string('petugas')->nullable();
                $table->timestamps();

                $table->foreign('laporan_keamanan_id')->references('id')->on('laporan_keamanans')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_keamanan_histories');
        Schema::dropIfExists('cctv_recordings');
        Schema::dropIfExists('cctvs');

        Schema::table('laporan_keamanans', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_laporan',
                'nama_pelapor',
                'kontak_pelapor',
                'cctv_id',
                'dokumentasi_penanganan',
                'selesai_at',
            ]);
        });

        Schema::table('jadwal_rondas', function (Blueprint $table) {
            $table->dropColumn([
                'koordinator',
                'kontak_koordinator',
                'laporan_patroli',
                'dokumentasi_patroli',
            ]);
        });
    }
};
