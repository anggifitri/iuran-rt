<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add tenant_id and email_resmi to wilayah_rw
        Schema::table('wilayah_rw', function (Blueprint $table) {
            if (!Schema::hasColumn('wilayah_rw', 'tenant_id')) {
                $table->string('tenant_id')->default('TENANT-RW018-MEKARJAYA')->after('rw_number')->index();
            }
            if (!Schema::hasColumn('wilayah_rw', 'email_resmi')) {
                $table->string('email_resmi')->default('sekretariat@rw018.nexanest.id')->after('ketua_rw_phone')->nullable();
            }
        });

        // 2. Create wilayah_rt table
        if (!Schema::hasTable('wilayah_rt')) {
            Schema::create('wilayah_rt', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('wilayah_rw_id')->nullable()->index();
                $table->string('rw_number', 10)->default('018')->index();
                $table->string('rt_number', 10)->index();
                $table->string('nama_ketua_rt')->nullable();
                $table->string('phone_ketua_rt', 25)->nullable();
                $table->unsignedBigInteger('admin_user_id')->nullable()->index();
                $table->string('status', 20)->default('active')->index(); // 'active', 'inactive'
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->unique(['rw_number', 'rt_number'], 'unique_rw_rt');
                $table->foreign('wilayah_rw_id')->references('id')->on('wilayah_rw')->onDelete('cascade');
                $table->foreign('admin_user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 3. Seed initial 5 RTs for RW 018
        $rw = DB::table('wilayah_rw')->where('rw_number', '018')->first();
        $rwId = $rw ? $rw->id : null;

        $rts = [
            [
                'rt_number' => '006',
                'nama_ketua_rt' => 'Fathir Hartono',
                'phone_ketua_rt' => '085098964187',
                'admin_user_id' => 3,
                'catatan' => 'Wilayah RT 006 Blok A-B',
            ],
            [
                'rt_number' => '007',
                'nama_ketua_rt' => 'Gavin Gunawan',
                'phone_ketua_rt' => '081179591231',
                'admin_user_id' => 4,
                'catatan' => 'Wilayah RT 007 Blok C-D',
            ],
            [
                'rt_number' => '008',
                'nama_ketua_rt' => 'Rizky Pramudya',
                'phone_ketua_rt' => '089529991390',
                'admin_user_id' => 5,
                'catatan' => 'Wilayah RT 008 Blok E-F (Pusat Balai Warga)',
            ],
            [
                'rt_number' => '009',
                'nama_ketua_rt' => 'Rian Sucipto',
                'phone_ketua_rt' => '082468958566',
                'admin_user_id' => 6,
                'catatan' => 'Wilayah RT 009 Blok G-H',
            ],
            [
                'rt_number' => '010',
                'nama_ketua_rt' => 'Bagas Kusumah',
                'phone_ketua_rt' => '086062540025',
                'admin_user_id' => 7,
                'catatan' => 'Wilayah RT 010 Blok I-J',
            ],
        ];

        foreach ($rts as $item) {
            $existing = DB::table('wilayah_rt')
                ->where('rw_number', '018')
                ->where('rt_number', $item['rt_number'])
                ->first();

            if (!$existing) {
                $userExists = $item['admin_user_id'] ? DB::table('users')->where('id', $item['admin_user_id'])->exists() : false;
                $rwExists = $rwId ? DB::table('wilayah_rw')->where('id', $rwId)->exists() : false;

                DB::table('wilayah_rt')->insert([
                    'wilayah_rw_id' => $rwExists ? $rwId : null,
                    'rw_number' => '018',
                    'rt_number' => $item['rt_number'],
                    'nama_ketua_rt' => $item['nama_ketua_rt'],
                    'phone_ketua_rt' => $item['phone_ketua_rt'],
                    'admin_user_id' => $userExists ? $item['admin_user_id'] : null,
                    'status' => 'active',
                    'catatan' => $item['catatan'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wilayah_rt');

        Schema::table('wilayah_rw', function (Blueprint $table) {
            if (Schema::hasColumn('wilayah_rw', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }
            if (Schema::hasColumn('wilayah_rw', 'email_resmi')) {
                $table->dropColumn('email_resmi');
            }
        });
    }
};
