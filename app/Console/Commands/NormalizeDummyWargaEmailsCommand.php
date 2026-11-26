<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Warga;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NormalizeDummyWargaEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexanest:normalize-dummy-warga-emails {--dry-run : Simulasi tanpa menyimpan ke database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ubah alamat email akun role warga menjadi format berurutan warga1@gmail.com, warga2@gmail.com, dst (urut RT lalu ID)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = (bool) $this->option('dry-run');

        $this->info("================================================================================");
        $this->info("    NORMALISASI EMAIL DUMMY WARGA (warga1@gmail.com, warga2@gmail.com, ...)     ");
        $this->info("================================================================================");

        if ($isDryRun) {
            $this->warn("[DRY RUN MODE] Perubahan hanya disimulasikan dan TIDAK akan disimpan ke database.");
        }

        // Ambil HANYA akun dengan role 'warga', diurutkan berdasarkan rt_number lalu id
        $wargaUsers = User::where('role', 'warga')
            ->orderBy('rt_number', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalWarga = $wargaUsers->count();
        if ($totalWarga === 0) {
            $this->warn("Tidak ditemukan akun dengan role 'warga'.");
            return 0;
        }

        $this->line("Ditemukan {$totalWarga} akun warga untuk dinormalisasi.\n");

        $tableRows = [];
        $hasWargaEmailCol = Schema::hasColumn('wargas', 'email');

        DB::beginTransaction();
        try {
            // TAHAP 1: Berikan email sementara (temporary) unik untuk menghindari bentrok constraint UNIQUE
            $this->line("1. Menerapkan email sementara (Stage 1 - Anti Unique Collision)...");
            $timestamp = now()->timestamp;
            foreach ($wargaUsers as $user) {
                $tempEmail = "temp_warga_{$user->id}_{$timestamp}@gmail.com";
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['email' => $tempEmail]);

                if ($hasWargaEmailCol && $user->warga_id) {
                    DB::table('wargas')
                        ->where('id', $user->warga_id)
                        ->update(['email' => $tempEmail]);
                }
            }

            // TAHAP 2: Set email permanen berurutan: warga1@gmail.com, warga2@gmail.com, ...
            $this->line("2. Menetapkan email berurutan warga1@gmail.com s/d warga{$totalWarga}@gmail.com (Stage 2)...");
            foreach ($wargaUsers as $index => $user) {
                $number = $index + 1;
                $newEmail = "warga{$number}@gmail.com";
                $oldEmail = $user->email;

                if (!$isDryRun) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'email' => $newEmail,
                            'updated_at' => now(),
                        ]);

                    if ($hasWargaEmailCol && $user->warga_id) {
                        DB::table('wargas')
                            ->where('id', $user->warga_id)
                            ->update(['email' => $newEmail]);
                    }
                }

                // Ambil info jenis kelamin
                $gender = $user->gender ?? '-';
                $genderLabel = ($gender === 'P' || $gender === 'Perempuan') ? 'Perempuan (P)' : 'Laki-laki (L)';

                $tableRows[] = [
                    $number,
                    $newEmail,
                    $user->name,
                    $user->rt_number ?? '000',
                    $genderLabel,
                    $oldEmail,
                    $user->id,
                ];
            }

            if ($isDryRun) {
                DB::rollBack();
                $this->warn("\n[DRY RUN] Transaksi di-rollback. Database tidak berubah.");
            } else {
                DB::commit();
                $this->info("\n[BERHASIL] Seluruh perubahan email warga telah disimpan ke database!");
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Terjadi kesalahan saat memproses normalisasi email: " . $e->getMessage());
            return 1;
        }

        // Tampilkan tabel hasil
        $this->table(
            ['No', 'Email Baru', 'Nama Warga', 'RT', 'Gender', 'Email Sebelumnya', 'User ID'],
            $tableRows
        );

        $this->info("\nRingkasan:");
        $this->line(" - Total Akun Warga Dinormalisasi: {$totalWarga}");
        $this->line(" - Rentang Email: warga1@gmail.com s/d warga{$totalWarga}@gmail.com");
        $this->line(" - Role Lain (Admin RW / RT / Bendahara / Super Admin): AMAN & TIDAK DIUBAH");

        return 0;
    }
}
