<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengumumans', function (Blueprint $table) {
            if (!Schema::hasColumn('pengumumans', 'prioritas')) {
                $table->string('prioritas', 20)->default('informasi')->after('kategori');
            }
            if (!Schema::hasColumn('pengumumans', 'tanggal_berakhir')) {
                $table->date('tanggal_berakhir')->nullable()->after('published_at');
            }
            if (!Schema::hasColumn('pengumumans', 'requires_rsvp')) {
                $table->boolean('requires_rsvp')->default(false)->after('is_pinned');
            }
            if (!Schema::hasColumn('pengumumans', 'rsvp_type')) {
                $table->string('rsvp_type', 30)->nullable()->after('requires_rsvp');
            }
            if (!Schema::hasColumn('pengumumans', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->after('status');
            }
            if (!Schema::hasColumn('pengumumans', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('is_archived');
            }
            if (!Schema::hasColumn('pengumumans', 'is_kegiatan')) {
                $table->boolean('is_kegiatan')->default(false)->after('archived_at');
            }
            if (!Schema::hasColumn('pengumumans', 'kegiatan_tanggal')) {
                $table->date('kegiatan_tanggal')->nullable()->after('is_kegiatan');
            }
            if (!Schema::hasColumn('pengumumans', 'kegiatan_waktu_mulai')) {
                $table->string('kegiatan_waktu_mulai', 10)->nullable()->after('kegiatan_tanggal');
            }
            if (!Schema::hasColumn('pengumumans', 'kegiatan_waktu_selesai')) {
                $table->string('kegiatan_waktu_selesai', 10)->nullable()->after('kegiatan_waktu_mulai');
            }
            if (!Schema::hasColumn('pengumumans', 'kegiatan_lokasi')) {
                $table->string('kegiatan_lokasi')->nullable()->after('kegiatan_waktu_selesai');
            }
            if (!Schema::hasColumn('pengumumans', 'agenda_rapat_id')) {
                $table->unsignedBigInteger('agenda_rapat_id')->nullable()->after('kegiatan_lokasi');
            }
        });

        if (!Schema::hasTable('pengumuman_reads')) {
            Schema::create('pengumuman_reads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pengumuman_id')->constrained('pengumumans')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamp('read_at')->useCurrent();
                $table->timestamps();

                $table->unique(['pengumuman_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('pengumuman_rsvps')) {
            Schema::create('pengumuman_rsvps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pengumuman_id')->constrained('pengumumans')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('warga_id')->nullable()->constrained('wargas')->onDelete('set null');
                $table->string('response', 30); // hadir, tidak_hadir, ikut, tidak_ikut, konfirmasi
                $table->text('catatan')->nullable();
                $table->timestamp('responded_at')->useCurrent();
                $table->timestamps();

                $table->unique(['pengumuman_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman_rsvps');
        Schema::dropIfExists('pengumuman_reads');

        Schema::table('pengumumans', function (Blueprint $table) {
            $table->dropColumn([
                'prioritas',
                'tanggal_berakhir',
                'requires_rsvp',
                'rsvp_type',
                'is_archived',
                'archived_at',
                'is_kegiatan',
                'kegiatan_tanggal',
                'kegiatan_waktu_mulai',
                'kegiatan_waktu_selesai',
                'kegiatan_lokasi',
                'agenda_rapat_id',
            ]);
        });
    }
};
