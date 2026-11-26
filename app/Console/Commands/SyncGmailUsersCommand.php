<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Warga;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncGmailUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:sync-gmail {--dry-run : Run without saving changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seragamkan alamat email seluruh pengguna dummy/lokal ke domain @gmail.com yang unik';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $this->info("=== SINKRONISASI EMAIL @GMAIL.COM & RELASI WARGA-USER ===");
        if ($isDryRun) {
            $this->warn("[DRY RUN MODE] Perubahan tidak akan disimpan ke database.");
        }

        // 1. Sinkronisasi dua arah relasi Warga <-> User
        $this->info("\n1. Memeriksa & menyinkronkan foreign key user_id dan warga_id...");
        $users = User::all();
        $linkedCount = 0;

        foreach ($users as $user) {
            $warga = $user->warga
                ?? Warga::where('user_id', $user->id)->first()
                ?? ($user->nik ? Warga::where('nik', $user->nik)->first() : null)
                ?? Warga::where('nama', $user->name)->first();

            if ($warga) {
                $needsSaveUser = false;
                $needsSaveWarga = false;

                if ($user->warga_id !== $warga->id) {
                    $user->warga_id = $warga->id;
                    $needsSaveUser = true;
                }
                if ($warga->user_id !== $user->id) {
                    $warga->user_id = $user->id;
                    $needsSaveWarga = true;
                }

                if (!$isDryRun) {
                    if ($needsSaveUser) $user->saveQuietly();
                    if ($needsSaveWarga) $warga->saveQuietly();
                }
                $linkedCount++;
            }
        }
        $this->line("   - {$linkedCount} akun berhasil dipastikan terhubung dengan profil Warga.");

        // 2. Kumpulkan email yang sudah berakhiran @gmail.com agar tidak tertimpa
        $this->info("\n2. Menyeragamkan email pengguna ke format @gmail.com unik...");
        $existingEmails = [];
        foreach (User::all() as $u) {
            $current = strtolower(trim($u->email));
            if (str_ends_with($current, '@gmail.com')) {
                $existingEmails[] = $current;
            }
        }

        $allUsers = User::orderBy('id')->get();
        $rows = [];
        $updatedCount = 0;

        DB::beginTransaction();
        try {
            foreach ($allUsers as $u) {
                $currentEmail = strtolower(trim($u->email));
                if (str_ends_with($currentEmail, '@gmail.com')) {
                    $rows[] = [
                        $u->id,
                        $u->name,
                        $u->role,
                        $u->rt_number ?: '000',
                        $u->email,
                        '(Sudah @gmail.com)',
                    ];
                    continue;
                }

                // Buat email baru unik berdasarkan nama
                $newEmail = $this->generateUniqueGmail($u->name, $u->rt_number, $existingEmails);
                $existingEmails[] = $newEmail;

                $rows[] = [
                    $u->id,
                    $u->name,
                    $u->role,
                    $u->rt_number ?: '000',
                    $u->email,
                    $newEmail,
                ];

                if (!$isDryRun) {
                    $u->email = $newEmail;
                    $u->email_verified_at = $u->email_verified_at ?: now();
                    $u->saveQuietly();
                }
                $updatedCount++;
            }

            if (!$isDryRun) {
                DB::commit();
            } else {
                DB::rollBack();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Gagal melakukan sinkronisasi email: " . $e->getMessage());
            return 1;
        }

        $this->table(
            ['ID', 'Nama', 'Role', 'RT', 'Email Lama', 'Email Baru (@gmail.com)'],
            $rows
        );

        $this->info("\nRingkasan:");
        $this->line(" - Total Akun: " . count($allUsers));
        $this->line(" - Akun Diperbarui: {$updatedCount}");
        $this->info("Sinkronisasi email @gmail.com selesai dengan sukses!");

        return 0;
    }

    /**
     * Generate format email unik berbasis nama pengguna.
     */
    protected function generateUniqueGmail(string $name, ?string $rt, array $taken): string
    {
        $clean = strtolower(trim($name));
        $clean = preg_replace('/[^a-z0-9]+/', '.', $clean);
        $clean = trim($clean, '.');
        if (empty($clean)) {
            $clean = 'warga';
        }

        $candidate = $clean . '@gmail.com';
        if (!in_array($candidate, $taken, true)) {
            return $candidate;
        }

        if ($rt) {
            $rtClean = preg_replace('/[^0-9]/', '', $rt);
            $rtCandidate = $clean . '.rt' . str_pad($rtClean, 3, '0', STR_PAD_LEFT) . '@gmail.com';
            if (!in_array($rtCandidate, $taken, true)) {
                return $rtCandidate;
            }
        }

        $counter = 2;
        while (in_array($clean . $counter . '@gmail.com', $taken, true)) {
            $counter++;
        }

        return $clean . $counter . '@gmail.com';
    }
}
