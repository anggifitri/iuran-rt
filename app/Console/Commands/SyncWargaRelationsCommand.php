<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Warga;
use App\Models\BankSampah;
use App\Models\BankSampahPenarikan;
use App\Models\Pembayaran;
use App\Models\PembayaranIuran;
use App\Models\SuratPengajuan;
use App\Models\Pengaduan;
use App\Models\PeminjamanFasilitas;
use App\Models\Umkm;
use App\Models\Iuran;
use App\Models\PosyanduAnak;
use App\Models\PosyanduBumil;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncWargaRelationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexanest:sync-warga-relations {--show-all : Tampilkan seluruh akun warga di ringkasan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi relasi foreign key antara akun login users (role warga), data kependudukan wargas, dan seluruh tabel modul/transaksi dummy';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("=== SINKRONISASI RELASI AKUN WARGA NEXANEST RW 018 ===");

        // 1. SINKRONISASI AKUN SPESIFIK: Budi Santoso & Rian Sucipto
        $this->info("1. Memeriksa & menyinkronkan akun khusus (Budi Santoso & Rian Sucipto)...");
        $this->syncSpecialWargaAccounts();

        // 2. HUBUNGKAN USERS (ROLE WARGA) ↔ WARGAS
        $this->info("2. Menyinkronkan foreign key users ↔ wargas...");
        $this->syncUsersWithWargas();

        // 3. SINKRONISASI DATA KELUARGA (KK) & POSYANDU (BALITA & BUMIL)
        $this->info("3. Menyinkronkan relasi KK dan modul Posyandu...");
        $this->syncFamilyAndPosyandu();

        // 4. SINKRONISASI TABEL TRANSAKSI BANK SAMPAH & PENARIKAN
        $this->info("4. Menyinkronkan transaksi Bank Sampah & Penarikan...");
        $this->syncBankSampah();

        // 5. SINKRONISASI TABEL PEMBAYARAN IURAN & TRANSAKSI KEUANGAN
        $this->info("5. Menyinkronkan data Pembayaran Iuran...");
        $this->syncPembayaranIuran();

        // 6. SINKRONISASI TABEL SURAT, PENGADUAN, UMKM, PEMINJAMAN FASILITAS
        $this->info("6. Menyinkronkan modul Surat, Pengaduan, UMKM, dan Fasilitas...");
        $this->syncModuleTables();

        // 7. VERIFIKASI & TAMPILKAN TABEL RINGKASAN
        $this->info("7. Menyiapkan ringkasan verifikasi data riil...");
        $this->displaySummaryTable();

        $this->info("\n[SUCCESS] Seluruh relasi akun warga dan data modul transaksi berhasil disinkronkan secara idempoten!");
        return Command::SUCCESS;
    }

    /**
     * Sinkronkan akun spesifik seperti Budi Santoso & Rian Sucipto agar siap login dan memiliki transaksi.
     */
    protected function syncSpecialWargaAccounts(): void
    {
        // A. Budi Santoso (Penyetor Bank Sampah di RT 006)
        $wBudi = Warga::where('nama', 'Budi Santoso')->first();
        if (!$wBudi) {
            $wBudi = Warga::create([
                'nama' => 'Budi Santoso',
                'rt_number' => '006',
                'rw_number' => '018',
                'nik' => '3275010600000001',
                'no_kk' => '3275010600000001',
                'gender' => 'L',
                'blok_rumah' => 'Blok B No. 15',
                'alamat' => 'Jl. Sakura No. 15, RT 006 RW 018',
                'nomor_hp' => '081234560061',
                'is_kk' => true,
                'status_warga' => 'tetap',
                'status_verifikasi' => 'terverifikasi',
            ]);
        }

        $uBudi = User::where('email', 'budi.santoso@rt.com')->orWhere('name', 'Budi Santoso')->first();
        if (!$uBudi) {
            $uBudi = User::create([
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@rt.com',
                'password' => Hash::make('password123'),
                'role' => 'warga',
                'rt_number' => '006',
                'rw_number' => '018',
                'phone' => '081234560061',
                'address' => 'Jl. Sakura No. 15, RT 006 RW 018',
                'nik' => $wBudi->nik,
                'no_kk' => $wBudi->no_kk,
                'warga_id' => $wBudi->id,
            ]);
        } else {
            $uBudi->update([
                'warga_id' => $wBudi->id,
                'nik' => $wBudi->nik,
                'no_kk' => $wBudi->no_kk,
                'rt_number' => '006',
            ]);
        }
        $wBudi->update(['user_id' => $uBudi->id]);

        // B. Rian Sucipto (Warga ID 1 di RT 009)
        $wRian = Warga::find(1) ?: Warga::where('nama', 'Rian Sucipto')->first();
        if ($wRian) {
            $uRian = User::where('email', 'rian.sucipto@rt.com')->first();
            if (!$uRian) {
                $uRian = User::create([
                    'name' => $wRian->nama,
                    'email' => 'rian.sucipto@rt.com',
                    'password' => Hash::make('password123'),
                    'role' => 'warga',
                    'rt_number' => $wRian->rt_number ?: '009',
                    'rw_number' => '018',
                    'phone' => $wRian->nomor_hp ?? '081234560091',
                    'address' => $wRian->alamat,
                    'nik' => $wRian->nik,
                    'no_kk' => $wRian->no_kk,
                    'warga_id' => $wRian->id,
                ]);
            } else {
                $uRian->update([
                    'warga_id' => $wRian->id,
                    'nik' => $wRian->nik,
                    'no_kk' => $wRian->no_kk,
                    'rt_number' => $wRian->rt_number ?: '009',
                ]);
            }
            $wRian->update(['user_id' => $uRian->id]);
        }
    }

    /**
     * Hubungkan setiap User role warga dengan record Warga.
     */
    protected function syncUsersWithWargas(): void
    {
        $wargaUsers = User::where('role', 'warga')->get();

        foreach ($wargaUsers as $user) {
            $warga = null;

            // 1. Cek warga_id yang sudah ada pada user
            if (!empty($user->warga_id)) {
                $warga = Warga::find($user->warga_id);
            }

            // 2. Cek user_id pada tabel wargas
            if (!$warga) {
                $warga = Warga::where('user_id', $user->id)->first();
            }

            // 3. Cek kesamaan NIK
            if (!$warga && !empty($user->nik)) {
                $warga = Warga::where('nik', $user->nik)->first();
            }

            // 4. Cek kesamaan nama exact
            if (!$warga && !empty($user->name)) {
                $warga = Warga::where('nama', $user->name)->first();
            }

            // 5. Cek kesamaan nama di RT yang sama
            if (!$warga && !empty($user->name) && !empty($user->rt_number)) {
                $warga = Warga::where('nama', 'like', '%' . trim($user->name) . '%')
                    ->where('rt_number', $user->rt_number)
                    ->first();
            }

            // 6. Jika belum ada pasangan di tabel wargas:
            // Buat record kependudukan baru di tabel wargas yang sesuai data user
            if (!$warga) {
                $nik = $user->nik ?: '327501' . rand(1000000000, 9999999999);
                $noKk = $user->no_kk ?: '327501' . rand(1000000000, 9999999999);

                $warga = Warga::create([
                    'user_id' => $user->id,
                    'nama' => $user->name,
                    'rt_number' => $user->rt_number ?? '010',
                    'rw_number' => $user->rw_number ?? '018',
                    'nik' => $nik,
                    'no_kk' => $noKk,
                    'gender' => 'L',
                    'blok_rumah' => 'Blok ' . chr(65 + ((int)($user->rt_number ?? 10) % 5)) . ' No. ' . $user->id,
                    'alamat' => $user->address ?? ('Jl. Lingkungan RW 018 RT ' . ($user->rt_number ?? '010')),
                    'nomor_hp' => $user->phone ?? '081234567890',
                    'is_kk' => true,
                    'status_warga' => 'tetap',
                    'status_verifikasi' => 'terverifikasi',
                ]);
            }

            // Sinkronkan dua arah
            if ($warga) {
                $wargaUpdates = [];
                if ($warga->user_id !== $user->id) {
                    $wargaUpdates['user_id'] = $user->id;
                }
                if (!empty($wargaUpdates)) {
                    $warga->update($wargaUpdates);
                }

                $userUpdates = [];
                if ($user->warga_id !== $warga->id) {
                    $userUpdates['warga_id'] = $warga->id;
                }
                if (empty($user->nik) || $user->nik !== $warga->nik) {
                    $userUpdates['nik'] = $warga->nik;
                }
                if (empty($user->no_kk) || $user->no_kk !== $warga->no_kk) {
                    $userUpdates['no_kk'] = $warga->no_kk;
                }
                if (!empty($userUpdates)) {
                    $user->update($userUpdates);
                }
            }
        }
    }

    /**
     * Sinkronkan relasi KK dan Posyandu.
     */
    protected function syncFamilyAndPosyandu(): void
    {
        // Pastikan Bagas Nugraha (warga_id 131) tetap berada pada KK orang tua kandungnya (Dimas Nugraha & Sisca Tanjung)
        $bagasWarga = Warga::find(131);
        if ($bagasWarga && $bagasWarga->no_kk !== '3275015690639748') {
            $bagasWarga->update(['no_kk' => '3275015690639748', 'rt_number' => '010']);
        }

        // Sinkronkan keselarasan RT anak posyandu dengan data warga anak
        foreach (PosyanduAnak::all() as $pa) {
            if (!empty($pa->warga_id)) {
                $w = Warga::find($pa->warga_id);
                if ($w && !empty($w->rt_number) && $pa->rt_number !== $w->rt_number) {
                    $pa->update(['rt_number' => $w->rt_number]);
                }
            }
        }

        // Sinkronkan keselarasan RT bumil posyandu dengan data warga bumil
        foreach (PosyanduBumil::all() as $pb) {
            if (!empty($pb->warga_id)) {
                $w = Warga::find($pb->warga_id);
                if ($w && !empty($w->rt_number) && $pb->rt_number !== $w->rt_number) {
                    $pb->update(['rt_number' => $w->rt_number]);
                }
            }
        }
    }

    /**
     * Sinkronkan tabel Bank Sampah & Penarikan.
     */
    protected function syncBankSampah(): void
    {
        $allSetorans = BankSampah::all();
        foreach ($allSetorans as $setoran) {
            $warga = null;
            $user = null;

            // 1. Dari warga_id
            if (!empty($setoran->warga_id)) {
                $warga = Warga::find($setoran->warga_id);
                if ($warga && !empty($warga->user_id)) {
                    $user = User::find($warga->user_id);
                }
            }

            // 2. Dari user_id
            if (!$warga && !empty($setoran->user_id)) {
                $user = User::find($setoran->user_id);
                if ($user && !empty($user->warga_id)) {
                    $warga = Warga::find($user->warga_id);
                }
            }

            // 3. Dari nama_penyetor
            if (!$warga && !empty($setoran->nama_penyetor)) {
                $warga = Warga::where('nama', $setoran->nama_penyetor)->first();
                if (!$warga) {
                    $user = User::where('name', $setoran->nama_penyetor)->first();
                    if ($user) {
                        $warga = $user->resolveWarga();
                    }
                }
            }

            // Update record bank sampah jika ada yang belum lengkap
            $updates = [];
            if ($warga && $setoran->warga_id !== $warga->id) {
                $updates['warga_id'] = $warga->id;
            }
            if ($user && $setoran->user_id !== $user->id) {
                $updates['user_id'] = $user->id;
            } elseif ($warga && !empty($warga->user_id) && $setoran->user_id !== $warga->user_id) {
                $updates['user_id'] = $warga->user_id;
            }

            if (!empty($updates)) {
                $setoran->update($updates);
            }
        }

        // Sinkronkan penarikan saldo
        $allPenarikans = BankSampahPenarikan::all();
        foreach ($allPenarikans as $penarikan) {
            $warga = null;
            if (!empty($penarikan->warga_id)) {
                $warga = Warga::find($penarikan->warga_id);
            }
            if ($warga && !empty($warga->user_id) && $penarikan->user_id !== $warga->user_id) {
                $penarikan->update(['user_id' => $warga->user_id]);
            }
        }

        // Pastikan akun sampel memiliki transaksi setoran bank sampah riil jika masih 0
        $sampleEmails = ['warga1@rt.com', 'warga3@rt.com', 'warga4@rt.com', 'warga26@rt.com', 'budi.santoso@rt.com', 'rian.sucipto@rt.com'];
        foreach ($sampleEmails as $email) {
            $u = User::where('email', $email)->first();
            if ($u) {
                $w = $u->resolveWarga();
                $existing = BankSampah::where('user_id', $u->id)
                    ->orWhere(function ($q) use ($w) {
                        if ($w) $q->where('warga_id', $w->id);
                    })->count();

                if ($existing === 0) {
                    BankSampah::create([
                        'nomor_transaksi' => 'BS-' . strtoupper(Str::random(6)),
                        'rt_number' => $u->rt_number ?: '006',
                        'warga_id' => $w ? $w->id : null,
                        'user_id' => $u->id,
                        'nama_penyetor' => $u->name,
                        'tanggal_setor' => Carbon::now()->subDays(12)->toDateString(),
                        'kategori' => 'plastik',
                        'berat_kg' => 6.5,
                        'satuan' => 'kg',
                        'harga_per_satuan' => 4000,
                        'nilai_rupiah' => 26000,
                        'catatan' => 'Setoran botol plastik bersih',
                        'status' => 'diterima',
                        'petugas' => 'Pengurus Lingkungan',
                    ]);

                    BankSampah::create([
                        'nomor_transaksi' => 'BS-' . strtoupper(Str::random(6)),
                        'rt_number' => $u->rt_number ?: '006',
                        'warga_id' => $w ? $w->id : null,
                        'user_id' => $u->id,
                        'nama_penyetor' => $u->name,
                        'tanggal_setor' => Carbon::now()->subDays(2)->toDateString(),
                        'kategori' => 'kardus',
                        'berat_kg' => 10.0,
                        'satuan' => 'kg',
                        'harga_per_satuan' => 2000,
                        'nilai_rupiah' => 20000,
                        'catatan' => 'Setoran kardus bekas packaging',
                        'status' => 'diterima',
                        'petugas' => 'Pengurus Lingkungan',
                    ]);
                }
            }
        }
    }

    /**
     * Sinkronkan tabel Pembayaran Iuran (`pembayarans` dan `transactions`).
     */
    protected function syncPembayaranIuran(): void
    {
        // Pastikan ada Master Iuran aktif
        $masterIuran = Iuran::first();
        if (!$masterIuran) {
            $masterIuran = Iuran::create([
                'name' => 'Iuran Pengelolaan Lingkungan RW 018',
                'description' => 'Iuran wajib bulanan warga RW 018',
                'amount' => 100000,
                'due_date' => Carbon::now()->endOfYear()->toDateString(),
                'type' => 'wajib',
                'is_active' => true,
            ]);
        }

        // A. Tabel `pembayarans` (PembayaranIuran model)
        $pembayarans = PembayaranIuran::all();
        foreach ($pembayarans as $p) {
            $warga = null;
            if (!empty($p->warga_id)) {
                $warga = Warga::find($p->warga_id);
            }

            if ($warga && !empty($warga->user_id)) {
                $updates = [];
                if ($p->user_id !== $warga->user_id) {
                    $updates['user_id'] = $warga->user_id;
                }
                if (!empty($warga->rt_number) && $p->rt_number !== $warga->rt_number) {
                    $updates['rt_number'] = $warga->rt_number;
                }
                if (!empty($updates)) {
                    $p->update($updates);
                }
            } elseif (!empty($p->user_id)) {
                $u = User::find($p->user_id);
                if ($u && !empty($u->warga_id) && empty($p->warga_id)) {
                    $p->update(['warga_id' => $u->warga_id]);
                }
            }
        }

        // B. Tabel `transactions` (Pembayaran model)
        $transactions = Pembayaran::all();
        foreach ($transactions as $t) {
            if (!empty($t->warga_id) && empty($t->created_by)) {
                $w = Warga::find($t->warga_id);
                if ($w && !empty($w->user_id)) {
                    $t->update(['created_by' => $w->user_id]);
                }
            }
        }

        // Pastikan akun sampel memiliki riwayat iuran terbayar untuk bulan 1 s/d 5 tahun 2026
        $sampleEmails = ['warga1@rt.com', 'warga3@rt.com', 'warga4@rt.com', 'warga26@rt.com', 'budi.santoso@rt.com', 'rian.sucipto@rt.com'];
        foreach ($sampleEmails as $email) {
            $u = User::where('email', $email)->first();
            if ($u) {
                $w = $u->resolveWarga();
                for ($m = 1; $m <= 5; $m++) {
                    PembayaranIuran::firstOrCreate([
                        'user_id' => $u->id,
                        'bulan' => $m,
                        'tahun' => 2026,
                    ], [
                        'warga_id' => $w ? $w->id : null,
                        'iuran_id' => $masterIuran->id,
                        'amount_paid' => 100000,
                        'nominal_tagihan' => 100000,
                        'payment_date' => Carbon::create(2026, $m, 10)->toDateString(),
                        'status' => 'confirmed',
                        'payment_method' => 'transfer',
                        'rt_number' => $u->rt_number ?: '006',
                        'notes' => 'Pembayaran iuran bulanan warga',
                    ]);

                    if ($w) {
                        Pembayaran::firstOrCreate([
                            'warga_id' => $w->id,
                            'tanggal' => Carbon::create(2026, $m, 10)->toDateString(),
                            'kategori' => 'Iuran Warga',
                        ], [
                            'rt_number' => $u->rt_number ?: '006',
                            'jumlah' => 100000,
                            'tipe' => 'masuk',
                            'keterangan' => 'Iuran Warga Bulan ' . $m . ' / 2026',
                            'status' => 'lunas',
                            'created_by' => $u->name,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Sinkronkan foreign key dan RT pada tabel modul: Surat, Pengaduan, UMKM, Fasilitas.
     */
    protected function syncModuleTables(): void
    {
        // 1. Surat Pengajuan
        $surats = SuratPengajuan::all();
        foreach ($surats as $s) {
            if (!empty($s->user_id)) {
                $u = User::find($s->user_id);
                if ($u && !empty($u->rt_number) && $s->rt_number !== $u->rt_number) {
                    $s->update(['rt_number' => $u->rt_number]);
                }
            }
        }

        // 2. Pengaduan
        $pengaduans = Pengaduan::all();
        foreach ($pengaduans as $pg) {
            if (!empty($pg->user_id)) {
                $u = User::find($pg->user_id);
                if ($u && !empty($u->rt_number) && $pg->rt_number !== $u->rt_number) {
                    $pg->update(['rt_number' => $u->rt_number]);
                }
            }
        }

        // 3. UMKM
        $umkms = Umkm::all();
        foreach ($umkms as $um) {
            if (!empty($um->user_id)) {
                $u = User::find($um->user_id);
                if ($u && !empty($u->rt_number) && $um->rt_number !== $u->rt_number) {
                    $um->update(['rt_number' => $u->rt_number]);
                }
            }
        }

        // 4. Peminjaman Fasilitas
        $fasilitas = PeminjamanFasilitas::all();
        foreach ($fasilitas as $f) {
            if (!empty($f->user_id)) {
                $u = User::find($f->user_id);
                if ($u) {
                    $fUpdates = [];
                    if (!empty($u->rt_number) && $f->rt_number !== $u->rt_number) {
                        $fUpdates['rt_number'] = $u->rt_number;
                    }
                    if (!empty($u->nik) && empty($f->nik_peminjam)) {
                        $fUpdates['nik_peminjam'] = $u->nik;
                    }
                    if (!empty($fUpdates)) {
                        $f->update($fUpdates);
                    }
                }
            }
        }

        // Pastikan akun sampel memiliki surat & pengaduan riil
        $sampleEmails = ['warga1@rt.com', 'warga3@rt.com', 'warga4@rt.com', 'warga26@rt.com', 'budi.santoso@rt.com', 'rian.sucipto@rt.com'];
        foreach ($sampleEmails as $email) {
            $u = User::where('email', $email)->first();
            if ($u) {
                $w = $u->resolveWarga();
                if (SuratPengajuan::where('user_id', $u->id)->count() === 0) {
                    SuratPengajuan::create([
                        'user_id' => $u->id,
                        'rt_number' => $u->rt_number ?: '006',
                        'jenis_surat' => 'Surat Pengantar Domisili',
                        'keperluan' => 'Pengurusan Administrasi Kependudukan',
                        'status' => 'disetujui_rt',
                    ]);
                }

                if (Pengaduan::where('user_id', $u->id)->count() === 0) {
                    Pengaduan::create([
                        'nomor_pengaduan' => 'ADU-' . strtoupper(Str::random(6)),
                        'user_id' => $u->id,
                        'title' => 'Lampu Penerangan Jalan Umum',
                        'content' => 'Mohon perbaikan lampu jalan di depan gang yang padam pada malam hari.',
                        'category' => 'infrastruktur',
                        'priority' => 'sedang',
                        'rt_number' => $u->rt_number ?: '006',
                        'location_details' => 'Jl. Kenanga RT ' . ($u->rt_number ?: '006'),
                        'status' => 'diproses',
                    ]);
                }
            }
        }
    }

    /**
     * Tampilkan tabel ringkasan hasil sinkronisasi.
     */
    protected function displaySummaryTable(): void
    {
        $showAll = $this->option('show-all');

        // Pilih sampel akun warga utama
        $targetEmails = [
            'warga1@rt.com',      // Adrian Pradana
            'warga3@rt.com',      // Adrian Tanjung
            'warga4@rt.com',      // Aisha Gunawan
            'warga26@rt.com',     // Bagas Tanjung
            'budi.santoso@rt.com',// Budi Santoso
            'rian.sucipto@rt.com',// Rian Sucipto
            'serabiell@rt.com',   // Ghiserabiella
        ];

        $query = User::where('role', 'warga');
        if (!$showAll) {
            $query->whereIn('email', $targetEmails);
        }
        $users = $query->orderBy('id', 'asc')->get();

        $rows = [];
        foreach ($users as $u) {
            $w = $u->resolveWarga();
            $wargaIdStr = $w ? "#{$w->id} (RT {$w->rt_number})" : '<fg=red>Belum Terhubung</>';
            $noKkStr = $w && !empty($w->no_kk) ? $w->no_kk : ($u->no_kk ?: '-');

            // Hitung data riil milik akun tersebut
            $wargaId = $w ? $w->id : 0;
            $userId = $u->id;
            $familyIds = $u->familyWargaIds();

            // 1. Bank Sampah (warga_id atau user_id atau nama)
            $bsCount = BankSampah::where(function ($q) use ($wargaId, $userId, $u) {
                $q->where('user_id', $userId)
                  ->orWhere('warga_id', $wargaId)
                  ->orWhere('nama_penyetor', $u->name);
            })->count();

            // 2. Iuran / Pembayaran
            $iuranCount = PembayaranIuran::where(function ($q) use ($wargaId, $userId) {
                $q->where('user_id', $userId)->orWhere('warga_id', $wargaId);
            })->count();
            if ($iuranCount === 0) {
                $iuranCount = Pembayaran::where('warga_id', $wargaId)->count();
            }

            // 3. Surat Pengajuan
            $suratCount = SuratPengajuan::where('user_id', $userId)->count();

            // 4. Pengaduan
            $pengaduanCount = Pengaduan::where('user_id', $userId)->count();

            // 5. Posyandu Balita & Bumil dalam 1 KK
            $posyanduCount = 0;
            if (!empty($familyIds)) {
                $posyanduCount = PosyanduAnak::whereIn('warga_id', $familyIds)->count()
                    + PosyanduBumil::whereIn('warga_id', $familyIds)->count();
            }

            $rows[] = [
                $u->name,
                $u->email,
                $wargaIdStr,
                $noKkStr,
                "{$bsCount} Tx",
                "{$iuranCount} Record",
                "{$suratCount} Surat",
                "{$pengaduanCount} Laporan",
                "{$posyanduCount} Pasien KK",
            ];
        }

        $this->table(
            ['Nama Akun', 'Email Login', 'Status Warga (ID/RT)', 'Nomor KK', 'Bank Sampah', 'Tagihan Iuran', 'Surat', 'Pengaduan', 'Posyandu KK'],
            $rows
        );
    }
}
