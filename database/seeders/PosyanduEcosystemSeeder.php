<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Posyandu;
use App\Models\PosyanduAnak;
use App\Models\PosyanduAnakPemeriksaan;
use App\Models\PosyanduAnakImunisasi;
use App\Models\PosyanduBumil;
use App\Models\PosyanduBumilPemeriksaan;
use App\Models\Warga;
use App\Models\User;
use App\Services\WhoGrowthStandardsService;
use Carbon\Carbon;

class PosyanduEcosystemSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin_rw')->orWhere('email', 'adminrw@rt.com')->first();
        $adminId = $admin ? $admin->id : null;

        // 1. Update/Clean Posyandu Schedule Statuses
        foreach (Posyandu::all() as $pos) {
            $tgl = Carbon::parse($pos->tanggal);
            if ($tgl->isPast()) {
                $pos->status = 'selesai';
            } else {
                $pos->status = 'terjadwal';
            }
            if (!$pos->jam_mulai) {
                $pos->jam_mulai = '08:30';
                $pos->jam_selesai = '11:30';
            }
            if (!$pos->penanggung_jawab) {
                $pos->penanggung_jawab = 'Bidan Siti Aminah, Amd.Keb';
            }
            $pos->save();
        }

        // Add 2 more upcoming schedules if needed
        if (Posyandu::whereDate('tanggal', '>=', now())->count() < 2) {
            Posyandu::create([
                'nama'             => 'Posyandu Terpadu Cempaka RW 018',
                'tanggal'          => Carbon::now()->addDays(7)->format('Y-m-d'),
                'lokasi'           => 'Balai Warga Utama RW 018',
                'keterangan'       => 'Pemeriksaan rutin bulanan tumbuh kembang balita, pemantauan kurva KMS, imunisasi PCV & Polio, serta pemeriksaan ANC ibu hamil.',
                'status'           => 'terjadwal',
                'kategori'         => 'Terpadu',
                'jam_mulai'        => '08:00',
                'jam_selesai'      => '11:30',
                'penanggung_jawab' => 'Bidan Siti Aminah, Amd.Keb & Kader Melati',
            ]);

            Posyandu::create([
                'nama'             => 'Penyuluhan Pencegahan Stunting & Kelas Ibu Hamil',
                'tanggal'          => Carbon::now()->addDays(18)->format('Y-m-d'),
                'lokasi'           => 'Balai Pertemuan RT 007',
                'keterangan'       => 'Konseling gizi seimbang balita, demo masak MP-ASI kaya protein hewani, skrining LiLA ibu hamil, dan pembagian suplemen zat besi.',
                'status'           => 'terjadwal',
                'kategori'         => 'Ibu Hamil',
                'jam_mulai'        => '09:00',
                'jam_selesai'      => '12:00',
                'penanggung_jawab' => 'Ahli Gizi Puskesmas & Kader Posyandu',
            ]);
        }

        // 2. Link & Enrich Posyandu Anak
        $standardVaccines = [
            ['nama' => 'Hepatitis B (HB0)', 'bulan' => 0],
            ['nama' => 'BCG',               'bulan' => 1],
            ['nama' => 'Polio 1 (OPV)',     'bulan' => 1],
            ['nama' => 'DPT-HB-Hib 1',      'bulan' => 2],
            ['nama' => 'Polio 2 (OPV)',     'bulan' => 2],
            ['nama' => 'PCV 1',             'bulan' => 2],
            ['nama' => 'Rotavirus 1',       'bulan' => 2],
            ['nama' => 'DPT-HB-Hib 2',      'bulan' => 3],
            ['nama' => 'Polio 3 (OPV)',     'bulan' => 3],
            ['nama' => 'PCV 2',             'bulan' => 3],
            ['nama' => 'Rotavirus 2',       'bulan' => 3],
            ['nama' => 'DPT-HB-Hib 3',      'bulan' => 4],
            ['nama' => 'Polio 4 (OPV)',     'bulan' => 4],
            ['nama' => 'IPV 1 (Suntik)',    'bulan' => 4],
            ['nama' => 'Campak-Rubela (MR)','bulan' => 9],
            ['nama' => 'PCV 3 (Booster)',   'bulan' => 12],
            ['nama' => 'DPT-HB-Hib Booster','bulan' => 18],
            ['nama' => 'MR Booster',        'bulan' => 18],
        ];

        foreach (PosyanduAnak::all() as $anak) {
            // Find Warga match
            $warga = Warga::where('nama', 'like', '%' . trim($anak->nama_anak) . '%')->first();
            if ($warga) {
                $anak->warga_id = $warga->id;
                $anak->nik = $warga->nik;
                $anak->jenis_kelamin = $warga->gender ?? 'L';
                $anak->tanggal_lahir = $warga->tanggal_lahir;
                $anak->rt_number = $warga->rt_number;
                $anak->alamat = $warga->alamat ?? "Jl. Perumahan RW 018 RT {$warga->rt_number}";

                // Calculate exact age in months from birthdate
                if ($warga->tanggal_lahir) {
                    $birthDate = Carbon::parse($warga->tanggal_lahir);
                    $anak->umur_bulan = round($birthDate->diffInMonths(now()), 1);
                }

                // Find parents in KK
                if ($warga->no_kk) {
                    $kkFamily = Warga::where('no_kk', $warga->no_kk)->where('id', '!=', $warga->id)->get();
                    $ayah = $kkFamily->where('gender', 'L')->sortBy('tanggal_lahir')->first();
                    $ibu = $kkFamily->where('gender', 'P')->sortBy('tanggal_lahir')->first();
                    $anak->nama_ayah = $ayah ? $ayah->nama : 'Bpk. ' . explode(' ', $anak->nama_anak)[count(explode(' ', $anak->nama_anak))-1];
                    $anak->nama_ibu = $ibu ? $ibu->nama : 'Ibu Keluarga';
                    $anak->no_telepon = ($ibu && $ibu->no_telepon) ? $ibu->no_telepon : (($ayah && $ayah->no_telepon) ? $ayah->no_telepon : '0812' . rand(10000000, 99999999));
                }
            } else {
                $anak->jenis_kelamin = $anak->jenis_kelamin ?? 'L';
                $anak->rt_number = $anak->rt_number ?? '007';
                $anak->alamat = $anak->alamat ?? 'Jl. Melati Blok B No. 12 RT 007';
                if (!$anak->tanggal_lahir) {
                    $anak->tanggal_lahir = now()->subMonths(round($anak->umur_bulan ?? 12))->format('Y-m-d');
                }
            }

            if (!$anak->berat_lahir) {
                $anak->berat_lahir = rand(29, 36) / 10; // 2.9 - 3.6 kg
                $anak->tinggi_lahir = rand(48, 52); // 48 - 52 cm
                $anak->lingkar_kepala_lahir = rand(33, 35);
                $anak->golongan_darah = ['A', 'B', 'AB', 'O'][rand(0, 3)];
            }

            // Classification check
            $cls = WhoGrowthStandardsService::classifyBbU($anak->jenis_kelamin, $anak->umur_bulan, $anak->berat_badan);
            $clsTb = WhoGrowthStandardsService::classifyTbU($anak->jenis_kelamin, $anak->umur_bulan, $anak->tinggi_badan);
            
            // Check need follow up
            $needFollowUp = (
                str_contains(strtolower($anak->status_tumbuh), 'kurang') ||
                str_contains(strtolower($anak->status_tumbuh), 'stunt') ||
                str_contains(strtolower($cls['status']), 'kurang') ||
                str_contains(strtolower($clsTb['status']), 'pendek')
            );
            $anak->perlu_tindak_lanjut = $needFollowUp;
            if ($needFollowUp) {
                $anak->alasan_tindak_lanjut = $anak->status_tumbuh . ' (Perlu pendampingan PMT & pemantauan berkala)';
            }
            $anak->save();

            // Populate chronological examinations (2 to 4 points leading to current)
            $existingPems = PosyanduAnakPemeriksaan::where('posyandu_anak_id', $anak->id)->count();
            if ($existingPems === 0) {
                $currentAge = $anak->umur_bulan;
                $points = [];

                if ($currentAge >= 6) {
                    // Birth measurement
                    $points[] = [
                        'months_ago' => $currentAge,
                        'age'        => 0,
                        'bb'         => $anak->berat_lahir,
                        'tb'         => $anak->tinggi_lahir,
                        'lk'         => $anak->lingkar_kepala_lahir,
                        'catatan'    => 'Pemeriksaan neonatal baru lahir normal dan sehat.',
                    ];
                }

                if ($currentAge >= 12) {
                    $pAge1 = round($currentAge * 0.4, 1);
                    $table = WhoGrowthStandardsService::getBbUTable($anak->jenis_kelamin);
                    $inter = WhoGrowthStandardsService::interpolatePoints($table, $pAge1);
                    $tableTb = WhoGrowthStandardsService::getTbUTable($anak->jenis_kelamin);
                    $interTb = WhoGrowthStandardsService::interpolatePoints($tableTb, $pAge1);

                    $points[] = [
                        'months_ago' => $currentAge - $pAge1,
                        'age'        => $pAge1,
                        'bb'         => round($inter[3] + (rand(-4, 4) / 10), 2),
                        'tb'         => round($interTb[3] + (rand(-8, 8) / 10), 1),
                        'lk'         => round(42 + ($pAge1 * 0.4), 1),
                        'catatan'    => 'Penimbangan rutin posyandu. Nafsu makan anak baik, ASI dilanjutkan.',
                    ];
                }

                if ($currentAge >= 4) {
                    $pAge2 = max(1, round($currentAge - 2, 1));
                    $table = WhoGrowthStandardsService::getBbUTable($anak->jenis_kelamin);
                    $inter = WhoGrowthStandardsService::interpolatePoints($table, $pAge2);
                    $tableTb = WhoGrowthStandardsService::getTbUTable($anak->jenis_kelamin);
                    $interTb = WhoGrowthStandardsService::interpolatePoints($tableTb, $pAge2);

                    $points[] = [
                        'months_ago' => 2,
                        'age'        => $pAge2,
                        'bb'         => round($anak->berat_badan - 0.4, 2),
                        'tb'         => round($anak->tinggi_badan - 1.2, 1),
                        'lk'         => round(44 + ($pAge2 * 0.2), 1),
                        'catatan'    => 'Pemeriksaan berkala bulan sebelumnya. Edukasi gizi seimbang MP-ASI.',
                    ];
                }

                // Latest current measurement
                $points[] = [
                    'months_ago' => 0,
                    'age'        => $currentAge,
                    'bb'         => $anak->berat_badan,
                    'tb'         => $anak->tinggi_badan,
                    'lk'         => round(44 + ($currentAge * 0.25), 1),
                    'catatan'    => $anak->solusi ?? 'Pemeriksaan terkini di Posyandu Melati.',
                ];

                foreach ($points as $pt) {
                    $tgl = now()->subMonths(round($pt['months_ago']));
                    $clsBb = WhoGrowthStandardsService::classifyBbU($anak->jenis_kelamin, $pt['age'], $pt['bb']);
                    $clsTb = WhoGrowthStandardsService::classifyTbU($anak->jenis_kelamin, $pt['age'], $pt['tb']);
                    $clsImt = WhoGrowthStandardsService::classifyImtU($anak->jenis_kelamin, $pt['age'], ($pt['bb'] / (($pt['tb']/100)*($pt['tb']/100))));

                    PosyanduAnakPemeriksaan::create([
                        'posyandu_anak_id'    => $anak->id,
                        'posyandu_id'         => null,
                        'tanggal_pemeriksaan' => $tgl->format('Y-m-d'),
                        'umur_bulan'          => $pt['age'],
                        'berat_badan'         => $pt['bb'],
                        'tinggi_badan'        => $pt['tb'],
                        'lingkar_kepala'      => $pt['lk'],
                        'lingkar_lengan'      => round(13 + ($pt['age'] * 0.05), 1),
                        'imt'                 => round($pt['bb'] / (($pt['tb']/100)*($pt['tb']/100)), 2),
                        'status_bb_u'         => $clsBb['status'],
                        'status_tb_u'         => $clsTb['status'],
                        'status_bb_tb'        => $clsImt['status'],
                        'status_imt_u'        => $clsImt['status'],
                        'status_tumbuh'       => $anak->status_tumbuh ?? $clsBb['status'],
                        'asi_eksklusif'       => $pt['age'] <= 6 ? 'Ya' : 'Selesai',
                        'vitamin_a'           => $pt['age'] >= 6 && ($tgl->month === 2 || $tgl->month === 8),
                        'obat_cacing'         => $pt['age'] >= 12,
                        'catatan_petugas'     => $pt['catatan'],
                        'petugas_nama'        => 'Bidan Siti Aminah, Amd.Keb',
                        'created_by'          => $adminId,
                    ]);
                }
            }

            // Populate Immunizations
            $existingVax = PosyanduAnakImunisasi::where('posyandu_anak_id', $anak->id)->count();
            if ($existingVax === 0) {
                $checkedFromOld = is_array($anak->imunisasi_checked) ? $anak->imunisasi_checked : [];
                $birthDate = Carbon::parse($anak->tanggal_lahir ?? now()->subMonths(round($anak->umur_bulan)));

                foreach ($standardVaccines as $vax) {
                    $schedDate = (clone $birthDate)->addMonths($vax['bulan']);
                    $isPast = $schedDate->isPast();

                    // Check if old data had it checked
                    $matchedOld = false;
                    foreach ($checkedFromOld as $chk) {
                        if (str_contains(strtolower($vax['nama']), strtolower(trim($chk)))) {
                            $matchedOld = true;
                            break;
                        }
                    }

                    if ($isPast || $matchedOld) {
                        $status = 'Sudah Diberikan';
                        $tglBeri = $schedDate->format('Y-m-d');
                    } else {
                        $status = 'Terjadwal';
                        $tglBeri = null;
                    }

                    PosyanduAnakImunisasi::create([
                        'posyandu_anak_id'  => $anak->id,
                        'vaksin_nama'       => $vax['nama'],
                        'tanggal_pemberian' => $tglBeri,
                        'status'            => $status,
                        'jadwal_berikutnya' => $status === 'Terjadwal' ? $schedDate->format('Y-m-d') : null,
                        'batch_nomor'       => $status === 'Sudah Diberikan' ? 'VAX-' . rand(1000, 9999) : null,
                        'petugas_nama'      => $status === 'Sudah Diberikan' ? 'Bidan Siti Aminah, Amd.Keb' : null,
                        'catatan_petugas'   => $status === 'Sudah Diberikan' ? 'Vaksinasi berhasil diberikan tanpa efek KIPI berarti.' : 'Jadwal pemberian di Posyandu Melati RW 018.',
                    ]);
                }
            }
        }

        // 3. Link & Enrich Posyandu Bumil
        foreach (PosyanduBumil::all() as $bumil) {
            $warga = Warga::where('nama', 'like', '%' . trim($bumil->nama_ibu) . '%')->first();
            if ($warga) {
                $bumil->warga_id = $warga->id;
                $bumil->nik = $warga->nik;
                $bumil->tanggal_lahir = $warga->tanggal_lahir;
                $bumil->rt_number = $warga->rt_number;
                $bumil->alamat = $warga->alamat ?? "Jl. Anggrek RW 018 RT {$warga->rt_number}";
                if ($warga->no_kk) {
                    $suami = Warga::where('no_kk', $warga->no_kk)->where('gender', 'L')->first();
                    $bumil->nama_suami = $suami ? $suami->nama : 'Bpk. ' . explode(' ', $bumil->nama_ibu)[count(explode(' ', $bumil->nama_ibu))-1];
                    $bumil->no_telepon = $warga->no_telepon ?? ($suami ? $suami->no_telepon : '0813' . rand(10000000, 99999999));
                }
            } else {
                $bumil->rt_number = $bumil->rt_number ?? '008';
                $bumil->alamat = $bumil->alamat ?? 'Jl. Melati Blok C No. 5 RT 008';
                $bumil->nama_suami = $bumil->nama_suami ?? 'Bpk. Pendamping';
                $bumil->no_telepon = $bumil->no_telepon ?? '0813' . rand(10000000, 99999999);
            }

            $currentWeeks = $bumil->usia_kehamilan_minggu ?? 20;
            $hphtDate = now()->subWeeks($currentWeeks);
            $bumil->hpht = $hphtDate->format('Y-m-d');
            $bumil->hpl = (clone $hphtDate)->addDays(280)->format('Y-m-d');
            $bumil->hamil_ke = $bumil->hamil_ke ?? rand(1, 3);
            $bumil->golongan_darah = $bumil->golongan_darah ?? ['A', 'B', 'AB', 'O'][rand(0, 3)];

            // Check need follow up
            $needFollowUp = (
                str_contains(strtolower($bumil->status_kesehatan), 'kek') ||
                str_contains(strtolower($bumil->status_kesehatan), 'hipertensi') ||
                str_contains(strtolower($bumil->status_kesehatan), 'anemia') ||
                ($bumil->lila && $bumil->lila < 23.5)
            );
            $bumil->perlu_tindak_lanjut = $needFollowUp;
            if ($needFollowUp) {
                $bumil->alasan_tindak_lanjut = $bumil->status_kesehatan . ' (Perlu pemantauan intensif bidan desa)';
            }
            $bumil->save();

            // Populate chronological ANC examinations
            $existingAnc = PosyanduBumilPemeriksaan::where('posyandu_bumil_id', $bumil->id)->count();
            if ($existingAnc === 0) {
                $ancPoints = [];

                if ($currentWeeks >= 10) {
                    $ancPoints[] = [
                        'weeks'   => 8,
                        'bb'      => round($bumil->berat_badan - 3.5, 1),
                        'td'      => '115/75',
                        'tfu'     => 12,
                        'djj'     => '142 bpm (Doppler)',
                        'keluhan' => 'Mual ringan di pagi hari (morning sickness), nafsu makan sedikit berkurang.',
                        'ttd'     => 30,
                        'catatan' => 'Pemeriksaan ANC K1 Trimester 1. Diberikan suplemen Asam Folat dan B6.',
                    ];
                }

                if ($currentWeeks >= 20) {
                    $ancPoints[] = [
                        'weeks'   => 16,
                        'bb'      => round($bumil->berat_badan - 1.5, 1),
                        'td'      => '120/80',
                        'tfu'     => 18,
                        'djj'     => '148 bpm',
                        'keluhan' => 'Kondisi stabil, gerakan janin mulai terasa halus.',
                        'ttd'     => 30,
                        'catatan' => 'Pemeriksaan ANC K2 Trimester 2. Pertumbuhan janin sesuai kurva usia kehamilan.',
                    ];
                }

                // Current latest ANC
                $ancPoints[] = [
                    'weeks'   => $currentWeeks,
                    'bb'      => $bumil->berat_badan,
                    'td'      => $bumil->tekanan_darah,
                    'tfu'     => min(34, round($currentWeeks * 0.9, 1)),
                    'djj'     => '144 bpm',
                    'keluhan' => 'Punggung bawah terkadang pegal setelah berdiri lama.',
                    'ttd'     => 30,
                    'catatan' => $bumil->solusi ?? 'Pemeriksaan ANC berkala di Posyandu Melati.',
                ];

                foreach ($ancPoints as $anc) {
                    $tglPeriksa = (clone $hphtDate)->addWeeks($anc['weeks'])->format('Y-m-d');
                    PosyanduBumilPemeriksaan::create([
                        'posyandu_bumil_id'     => $bumil->id,
                        'posyandu_id'           => null,
                        'tanggal_pemeriksaan'   => $tglPeriksa,
                        'usia_kehamilan_minggu' => $anc['weeks'],
                        'berat_badan'           => $anc['bb'],
                        'tekanan_darah'         => $anc['td'],
                        'tinggi_fundus'         => $anc['tfu'],
                        'lila'                  => $bumil->lila,
                        'detak_jantung_janin'   => $anc['djj'],
                        'keluhan'               => $anc['keluhan'],
                        'pemberian_ttd'         => $anc['ttd'],
                        'status_kesehatan'      => $bumil->status_kesehatan,
                        'catatan_petugas'       => $anc['catatan'],
                        'jadwal_berikutnya'     => Carbon::parse($tglPeriksa)->addWeeks(4)->format('Y-m-d'),
                        'petugas_nama'          => 'Bidan Siti Aminah, Amd.Keb',
                        'created_by'            => $adminId,
                    ]);
                }
            }
        }
    }
}
