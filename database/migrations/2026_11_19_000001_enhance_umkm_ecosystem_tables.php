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
        // 1. Enhance umkms table
        Schema::table('umkms', function (Blueprint $table) {
            if (!Schema::hasColumn('umkms', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('umkms', 'status_verifikasi')) {
                $table->string('status_verifikasi', 30)->default('terverifikasi')->after('rt_number'); // 'terverifikasi', 'menunggu', 'ditolak'
            }
            if (!Schema::hasColumn('umkms', 'verified_at')) {
                $table->dateTime('verified_at')->nullable()->after('status_verifikasi');
            }
            if (!Schema::hasColumn('umkms', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('umkms', 'jam_operasional')) {
                $table->string('jam_operasional', 100)->default('08:00 - 20:00 WIB')->after('telepon');
            }
            if (!Schema::hasColumn('umkms', 'rating_avg')) {
                $table->decimal('rating_avg', 3, 2)->default(0.00)->after('status_verifikasi');
            }
            if (!Schema::hasColumn('umkms', 'rating_count')) {
                $table->unsignedInteger('rating_count')->default(0)->after('rating_avg');
            }
        });

        // 2. Create umkm_produks table
        if (!Schema::hasTable('umkm_produks')) {
            Schema::create('umkm_produks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('umkm_id')->constrained('umkms')->onDelete('cascade');
                $table->string('nama_produk', 150);
                $table->text('deskripsi')->nullable();
                $table->decimal('harga', 12, 2)->default(0);
                $table->string('foto')->nullable();
                $table->boolean('is_tersedia')->default(true);
                $table->timestamps();
            });
        }

        // 3. Create umkm_ulasans table
        if (!Schema::hasTable('umkm_ulasans')) {
            Schema::create('umkm_ulasans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('umkm_id')->constrained('umkms')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('nama_pembeli', 150);
                $table->unsignedTinyInteger('rating')->default(5); // 1 - 5
                $table->text('komentar');
                $table->boolean('is_verified_purchase')->default(true);
                $table->text('balasan_penjual')->nullable();
                $table->dateTime('balasan_at')->nullable();
                $table->boolean('is_reported')->default(false);
                $table->string('report_reason')->nullable();
                $table->boolean('is_hidden')->default(false);
                $table->timestamps();
            });
        }

        // 4. Create umkm_favorits table
        if (!Schema::hasTable('umkm_favorits')) {
            Schema::create('umkm_favorits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('umkm_id')->constrained('umkms')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['umkm_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkm_favorits');
        Schema::dropIfExists('umkm_ulasans');
        Schema::dropIfExists('umkm_produks');

        Schema::table('umkms', function (Blueprint $table) {
            $table->dropColumn([
                'user_id',
                'status_verifikasi',
                'verified_at',
                'verified_by',
                'jam_operasional',
                'rating_avg',
                'rating_count',
            ]);
        });
    }
};
