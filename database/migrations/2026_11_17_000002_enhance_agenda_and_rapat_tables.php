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
        // 1. Enhance agenda_rapats table
        Schema::table('agenda_rapats', function (Blueprint $table) {
            if (!Schema::hasColumn('agenda_rapats', 'waktu_selesai')) {
                $table->time('waktu_selesai')->nullable()->after('tanggal_waktu');
            }
            if (!Schema::hasColumn('agenda_rapats', 'jenis_rapat')) {
                $table->string('jenis_rapat', 50)->default('rw')->after('judul'); // 'rw', 'koordinasi_rt', 'musyawarah_warga'
            }
            if (!Schema::hasColumn('agenda_rapats', 'rt_number')) {
                $table->string('rt_number', 10)->nullable()->after('target_peserta'); // nullable for general RW, or '006'..'010'
            }
            if (!Schema::hasColumn('agenda_rapats', 'tindak_lanjut')) {
                $table->text('tindak_lanjut')->nullable()->after('hasil_keputusan');
            }
            if (!Schema::hasColumn('agenda_rapats', 'pic_tindak_lanjut')) {
                $table->string('pic_tindak_lanjut')->nullable()->after('tindak_lanjut');
            }
            if (!Schema::hasColumn('agenda_rapats', 'tenggat_tindak_lanjut')) {
                $table->date('tenggat_tindak_lanjut')->nullable()->after('pic_tindak_lanjut');
            }
            if (!Schema::hasColumn('agenda_rapats', 'lampiran_notulen')) {
                $table->string('lampiran_notulen')->nullable()->after('lampiran_dokumen');
            }
            if (!Schema::hasColumn('agenda_rapats', 'is_published_notulen')) {
                $table->boolean('is_published_notulen')->default(false)->after('status');
            }
            if (!Schema::hasColumn('agenda_rapats', 'published_at')) {
                $table->dateTime('published_at')->nullable()->after('is_published_notulen');
            }
        });

        // 2. Create agenda_kehadirans table
        if (!Schema::hasTable('agenda_kehadirans')) {
            Schema::create('agenda_kehadirans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('agenda_id')->constrained('agenda_rapats')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('nama_peserta', 150);
                $table->string('jabatan', 100)->nullable(); // e.g. 'Ketua RW', 'Ketua RT 007', 'Sekretaris', 'Warga'
                $table->string('rt_number', 10)->nullable();
                $table->string('status', 30)->default('hadir'); // 'hadir', 'tidak_hadir', 'izin'
                $table->string('keterangan')->nullable();
                $table->dateTime('waktu_kehadiran')->nullable();
                $table->timestamps();
            });
        }

        // 3. Create agenda_notulen_histories table
        if (!Schema::hasTable('agenda_notulen_histories')) {
            Schema::create('agenda_notulen_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('agenda_id')->constrained('agenda_rapats')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action_type', 50)->default('update'); // 'draft', 'publish', 'update'
                $table->text('ringkasan')->nullable();
                $table->text('hasil_keputusan')->nullable();
                $table->string('catatan_perubahan')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agenda_notulen_histories');
        Schema::dropIfExists('agenda_kehadirans');

        Schema::table('agenda_rapats', function (Blueprint $table) {
            $table->dropColumn([
                'waktu_selesai',
                'jenis_rapat',
                'rt_number',
                'tindak_lanjut',
                'pic_tindak_lanjut',
                'tenggat_tindak_lanjut',
                'lampiran_notulen',
                'is_published_notulen',
                'published_at',
            ]);
        });
    }
};
