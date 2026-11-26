<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create posyandu_petugas table (Penugasan Petugas Posyandu)
        if (!Schema::hasTable('posyandu_petugas')) {
            Schema::create('posyandu_petugas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('tipe_petugas'); // 'kader' atau 'tenaga_kesehatan'
                $table->string('jabatan'); // Contoh: 'Kader Posyandu Balita', 'Bidan Desa RW 018', 'Perawat Pembina'
                $table->string('nomor_sk_str')->nullable(); // Nomor SK Kader atau No STR/SIP Bidan
                $table->string('instansi')->nullable(); // Contoh: 'Puskesmas Kecamatan', 'Klinik Pratama', 'Posyandu Melati RW 018'
                $table->string('posyandu_nama')->nullable(); // Contoh: 'Posyandu Melati Mandiri', 'Semua Posyandu RW 018'
                $table->string('wilayah_rt')->default('semua'); // 'semua' atau '006', '007', '008', '009', '010'
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai')->nullable();
                $table->string('status')->default('aktif'); // 'aktif', 'nonaktif', 'dicabut'
                $table->string('status_verifikasi')->default('terverifikasi'); // 'terverifikasi', 'menunggu', 'ditolak'
                $table->string('kualifikasi_dokumen')->nullable();
                $table->text('catatan_penugasan')->nullable();
                $table->text('catatan_verifikasi')->nullable();
                $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('revoked_at')->nullable();
                $table->foreignId('revoked_by')->nullable()->constrained('users')->onDelete('set null');
                $table->string('revocation_reason')->nullable();
                $table->timestamps();
            });
        }

        // 2. Enhance posyandu_anak_pemeriksaans with Medical Verification fields
        Schema::table('posyandu_anak_pemeriksaans', function (Blueprint $table) {
            if (!Schema::hasColumn('posyandu_anak_pemeriksaans', 'is_verified_nakes')) {
                $table->boolean('is_verified_nakes')->default(false)->after('status_tumbuh');
            }
            if (!Schema::hasColumn('posyandu_anak_pemeriksaans', 'verified_by_nakes_id')) {
                $table->foreignId('verified_by_nakes_id')->nullable()->constrained('users')->onDelete('set null')->after('is_verified_nakes');
            }
            if (!Schema::hasColumn('posyandu_anak_pemeriksaans', 'verified_nakes_at')) {
                $table->timestamp('verified_nakes_at')->nullable()->after('verified_by_nakes_id');
            }
            if (!Schema::hasColumn('posyandu_anak_pemeriksaans', 'catatan_nakes')) {
                $table->text('catatan_nakes')->nullable()->after('verified_nakes_at');
            }
            if (!Schema::hasColumn('posyandu_anak_pemeriksaans', 'rekomendasi_rujukan')) {
                $table->string('rekomendasi_rujukan')->nullable()->after('catatan_nakes');
            }
        });

        // 3. Enhance posyandu_bumil_pemeriksaans with Medical Verification fields
        Schema::table('posyandu_bumil_pemeriksaans', function (Blueprint $table) {
            if (!Schema::hasColumn('posyandu_bumil_pemeriksaans', 'is_verified_nakes')) {
                $table->boolean('is_verified_nakes')->default(false)->after('status_kesehatan');
            }
            if (!Schema::hasColumn('posyandu_bumil_pemeriksaans', 'verified_by_nakes_id')) {
                $table->foreignId('verified_by_nakes_id')->nullable()->constrained('users')->onDelete('set null')->after('is_verified_nakes');
            }
            if (!Schema::hasColumn('posyandu_bumil_pemeriksaans', 'verified_nakes_at')) {
                $table->timestamp('verified_nakes_at')->nullable()->after('verified_by_nakes_id');
            }
            if (!Schema::hasColumn('posyandu_bumil_pemeriksaans', 'catatan_nakes')) {
                $table->text('catatan_nakes')->nullable()->after('verified_nakes_at');
            }
            if (!Schema::hasColumn('posyandu_bumil_pemeriksaans', 'rekomendasi_rujukan')) {
                $table->string('rekomendasi_rujukan')->nullable()->after('catatan_nakes');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posyandu_petugas');
    }
};
