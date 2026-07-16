<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warga;

class FixDataWargaLamaSeeder extends Seeder
{
    public function run()
    {
        Warga::query()->update([
            'is_kk' => true,
            'kk_id' => null,
            'rt_number' => '006', 
            'rw_number' => '018', 
            'alamat' => 'KOTA BANDUNG, Provinsi JAWA BARAT' 
        ]);
    }
}