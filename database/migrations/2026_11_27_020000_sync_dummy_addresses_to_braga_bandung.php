<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Idempotent migration to sync all dummy warga addresses, users, and Wilayah RW 018
     * to Jl. Braga, Kel. Braga, Kec. Sumur Bandung, Kota Bandung, Jawa Barat.
     */
    public function up(): void
    {
        // 1. Sinkronisasi Profil Wilayah RW 018 jika tabel wilayah_rw ada
        if (Schema::hasTable('wilayah_rw')) {
            DB::table('wilayah_rw')
                ->where('rw_number', '018')
                ->update([
                    'nama_wilayah' => 'RW 018 Kelurahan Braga',
                    'kelurahan' => 'Kelurahan Braga',
                    'kecamatan' => 'Kecamatan Sumur Bandung',
                    'kota' => 'Kota Bandung',
                    'provinsi' => 'Jawa Barat',
                    'kode_pos' => '40111',
                    'alamat_sekretariat' => 'Jl. Braga No. 18, RW 018, Kel. Braga, Kec. Sumur Bandung, Kota Bandung',
                    'tenant_id' => 'TENANT-RW018-BRAGA',
                    'updated_at' => now(),
                ]);
        }

        // 2. Sinkronisasi Data Alamat Warga
        if (Schema::hasTable('wargas')) {
            $allWargas = DB::table('wargas')->get();

            foreach ($allWargas as $w) {
                $rt = str_pad($w->rt_number ?: '006', 3, '0', STR_PAD_LEFT);
                $blokRumah = trim($w->blok_rumah ?: '');

                // Format alamat terstandarisasi Bandung Braga
                if (!empty($blokRumah)) {
                    $newAddress = "Jl. Braga, {$blokRumah}, RT {$rt} / RW 018, Kel. Braga, Kec. Sumur Bandung, Kota Bandung";
                } else {
                    $newAddress = "Jl. Braga, RT {$rt} / RW 018, Kel. Braga, Kec. Sumur Bandung, Kota Bandung";
                }

                DB::table('wargas')
                    ->where('id', $w->id)
                    ->update([
                        'alamat' => $newAddress,
                        'updated_at' => now(),
                    ]);

                // Sinkronkan juga ke akun user terkait bila ada
                if (!empty($w->user_id) && Schema::hasTable('users')) {
                    DB::table('users')
                        ->where('id', $w->user_id)
                        ->update([
                            'address' => $newAddress,
                            'rt_number' => $rt,
                            'updated_at' => now(),
                        ]);
                }
            }
        }

        // 3. Sinkronisasi Alamat User Lain yang berdomisili di RW 018 (jika belum sinkron)
        if (Schema::hasTable('users')) {
            $otherUsers = DB::table('users')
                ->whereNotNull('address')
                ->where(function ($q) {
                    $q->where('address', 'like', '%Depok%')
                      ->orWhere('address', 'like', '%Mekarjaya%')
                      ->orWhere('address', 'like', '%Merdeka Indah%');
                })
                ->get();

            foreach ($otherUsers as $u) {
                $rt = str_pad($u->rt_number ?: '006', 3, '0', STR_PAD_LEFT);
                $newAddress = "Jl. Braga, RT {$rt} / RW 018, Kel. Braga, Kec. Sumur Bandung, Kota Bandung";

                DB::table('users')
                    ->where('id', $u->id)
                    ->update([
                        'address' => $newAddress,
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
        // Operasi idempotent untuk data alamat tidak perlu membalikkan ke data tiruan Depok
    }
};
