<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembayaran;
use App\Models\Warga;
use Carbon\Carbon;

class PembayaranSeeder extends Seeder
{
    public function run()
    {
        // Ambil semua Kepala Keluarga (karena yang bayar iuran itu KK)
        $kepalaKeluargas = Warga::where('is_kk', true)->get();

        if ($kepalaKeluargas->isEmpty()) {
            return;
        }

        for ($i = 0; $i <= 5; $i++) {
            $bulanIni = Carbon::now()->startOfMonth()->subMonths($i);
            $wargaRajin = $kepalaKeluargas->random(min(40, $kepalaKeluargas->count()));

            foreach ($wargaRajin as $kk) {
                $tanggalBayar = $bulanIni->copy()->addDays(rand(0, 14));

                Pembayaran::create([
                    'warga_id'   => $kk->id,
                    'jenis'      => 'Iuran Bulanan',
                    'tipe'       => 'pemasukan',
                    'jumlah'     => 50000,
                    'keterangan' => 'Iuran Kas & Keamanan bulan ' . $bulanIni->format('F Y'),
                    'tanggal'    => $tanggalBayar->format('Y-m-d'),
                ]);
            }

            $pengeluarans = [
                ['Gaji Satpam & Petugas Sampah', 1500000],
                ['Bayar Listrik Fasum & Gapura', rand(25, 40) * 10000],
                ['Beli ATK, Kopi, & Konsumsi Rapat RT', rand(10, 20) * 10000],
                ['Perbaikan Infrastruktur / Lampu Jalan', rand(2, 10) * 100000]
            ];

            foreach ($pengeluarans as $pengeluaran) {
                $tanggalKeluar = $bulanIni->copy()->addDays(rand(15, 27));

                Pembayaran::create([
                    'warga_id'   => $kepalaKeluargas->first()->id,
                    'jenis'      => 'Pengeluaran Operasional',
                    'tipe'       => 'pengeluaran',
                    'jumlah'     => $pengeluaran[1],
                    'keterangan' => $pengeluaran[0],
                    'tanggal'    => $tanggalKeluar->format('Y-m-d'),
                ]);
            }
        }
    }
}
