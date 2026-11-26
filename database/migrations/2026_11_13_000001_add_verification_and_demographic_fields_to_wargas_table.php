<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('wargas')) {
            Schema::table('wargas', function (Blueprint $table) {
                if (!Schema::hasColumn('wargas', 'tempat_lahir')) {
                    $table->string('tempat_lahir')->nullable()->after('tanggal_lahir');
                }
                if (!Schema::hasColumn('wargas', 'agama')) {
                    $table->string('agama')->nullable()->default('Islam')->after('gender');
                }
                if (!Schema::hasColumn('wargas', 'status_perkawinan')) {
                    $table->string('status_perkawinan')->nullable()->default('Kawin')->after('agama');
                }
                if (!Schema::hasColumn('wargas', 'pekerjaan')) {
                    $table->string('pekerjaan')->nullable()->after('status_perkawinan');
                }
                if (!Schema::hasColumn('wargas', 'jenis_pengajuan')) {
                    $table->string('jenis_pengajuan')->nullable()->default('Pendaftaran Warga Baru')->after('status_verifikasi');
                }
                if (!Schema::hasColumn('wargas', 'data_sebelumnya')) {
                    $table->longText('data_sebelumnya')->nullable()->after('jenis_pengajuan');
                }
                if (!Schema::hasColumn('wargas', 'alasan_pengajuan')) {
                    $table->text('alasan_pengajuan')->nullable()->after('data_sebelumnya');
                }
                if (!Schema::hasColumn('wargas', 'pengaju_nama')) {
                    $table->string('pengaju_nama')->nullable()->after('alasan_pengajuan');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('wargas')) {
            Schema::table('wargas', function (Blueprint $table) {
                $columns = [
                    'tempat_lahir', 'agama', 'status_perkawinan', 'pekerjaan',
                    'jenis_pengajuan', 'data_sebelumnya', 'alasan_pengajuan', 'pengaju_nama'
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('wargas', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
