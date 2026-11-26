<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Alter Users Table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'rw_number')) {
                $table->string('rw_number')->default('018')->after('rt_number')->index();
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role')->index();
            }
            if (!Schema::hasColumn('users', 'last_active_at')) {
                $table->timestamp('last_active_at')->nullable()->after('updated_at');
            }
        });

        // 2. Wilayah RW Table (Multi-RW Registry)
        if (!Schema::hasTable('wilayah_rw')) {
            Schema::create('wilayah_rw', function (Blueprint $table) {
                $table->id();
                $table->string('rw_number')->unique();
                $table->string('nama_wilayah');
                $table->string('kelurahan')->default('Kelurahan Mekarjaya');
                $table->string('kecamatan')->default('Kecamatan Sukmajaya');
                $table->string('kota')->default('Kota Depok');
                $table->string('provinsi')->default('Jawa Barat');
                $table->string('kode_pos')->nullable()->default('16411');
                $table->text('alamat_sekretariat')->nullable();
                $table->string('ketua_rw_nama')->nullable();
                $table->string('ketua_rw_phone')->nullable();
                $table->unsignedBigInteger('admin_user_id')->nullable();
                $table->string('status')->default('active'); // active, suspended, inactive, pending
                $table->integer('total_rt')->default(8);
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        // 3. Pengajuan RW Baru Table (Self-service Registration & Review)
        if (!Schema::hasTable('pengajuan_rw')) {
            Schema::create('pengajuan_rw', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_pengajuan')->unique();
                $table->string('rw_number');
                $table->string('nama_wilayah');
                $table->string('nama_pemohon');
                $table->string('email_pemohon');
                $table->string('phone_pemohon');
                $table->string('nik_pemohon')->nullable();
                $table->string('kelurahan');
                $table->string('kecamatan');
                $table->string('kota');
                $table->string('provinsi');
                $table->text('alamat_sekretariat')->nullable();
                $table->string('dokumen_sk')->nullable();
                $table->string('status')->default('menunggu'); // menunggu, revisi, disetujui, ditolak
                $table->text('catatan_revisi')->nullable();
                $table->text('alasan_penolakan')->nullable();
                $table->text('catatan_admin')->nullable();
                $table->unsignedBigInteger('processed_by')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
            });
        }

        // 4. Super Admin Sensitive Access Audit Table
        if (!Schema::hasTable('super_admin_sensitive_access_logs')) {
            Schema::create('super_admin_sensitive_access_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('target_user_id')->nullable();
                $table->string('target_type'); // 'nik', 'phone', 'email', 'global_export', 'account_detail'
                $table->string('target_identifier')->nullable();
                $table->text('reason');
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();
            });
        }

        // 5. Platform Module Configs Table
        if (!Schema::hasTable('platform_module_configs')) {
            Schema::create('platform_module_configs', function (Blueprint $table) {
                $table->id();
                $table->string('module_key')->unique();
                $table->string('module_name');
                $table->string('icon')->default('fa-layer-group');
                $table->text('description')->nullable();
                $table->boolean('is_enabled_globally')->default(true);
                $table->text('disabled_rw_numbers')->nullable(); // JSON array
                $table->timestamps();
            });
        }

        // 6. Platform Global Notifications & System Alerts Table
        if (!Schema::hasTable('platform_notifications')) {
            Schema::create('platform_notifications', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('message');
                $table->string('type')->default('info'); // info, warning, critical, maintenance
                $table->string('target_scope')->default('all'); // all, rw, role
                $table->string('target_rw')->nullable();
                $table->string('target_role')->nullable();
                $table->boolean('is_banner')->default(false);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 7. Platform Backups Table
        if (!Schema::hasTable('platform_backups')) {
            Schema::create('platform_backups', function (Blueprint $table) {
                $table->id();
                $table->string('filename');
                $table->string('filesize')->default('0 KB');
                $table->string('type')->default('database'); // database, full
                $table->string('status')->default('success'); // success, failed
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // 8. Platform Global Settings Table
        if (!Schema::hasTable('platform_settings')) {
            Schema::create('platform_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('platform_backups');
        Schema::dropIfExists('platform_notifications');
        Schema::dropIfExists('platform_module_configs');
        Schema::dropIfExists('super_admin_sensitive_access_logs');
        Schema::dropIfExists('pengajuan_rw');
        Schema::dropIfExists('wilayah_rw');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'rw_number')) {
                $table->dropColumn('rw_number');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'last_active_at')) {
                $table->dropColumn('last_active_at');
            }
        });
    }
};
