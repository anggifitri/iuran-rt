<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schedule;
use App\Models\PlatformBackup;
use App\Models\PlatformSetting;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Automated scheduled platform database backup command.
 */
Artisan::command('platform:backup', function () {
    $this->info('Starting automated NexaNest platform backup...');
    $backupDir = storage_path('app/backups');
    if (!file_exists($backupDir)) {
        File::makeDirectory($backupDir, 0755, true);
    }
    $timestamp = date('Y-m-d_His');
    $filename = "nexanest_backup_auto_{$timestamp}.sqlite";
    $destPath = $backupDir . '/' . $filename;

    $sourcePath = config('database.connections.sqlite.database');
    if (file_exists($sourcePath)) {
        File::copy($sourcePath, $destPath);
        $bytes = filesize($destPath);
        $filesize = round($bytes / 1024 / 1024, 2) . ' MB';
        $checksum = hash_file('sha256', $destPath);

        PlatformBackup::create([
            'filename' => $filename,
            'filesize' => $filesize,
            'type' => 'database',
            'status' => 'success',
            'notes' => "SHA256: {$checksum} | Cadangan otomatis terjadwal",
            'created_by' => null,
        ]);

        // Retention policy enforcement
        $retentionDays = (int) PlatformSetting::get('backup_retention_days', 30);
        $cutoff = now()->subDays($retentionDays);
        $oldBackups = PlatformBackup::where('created_at', '<', $cutoff)->get();
        foreach ($oldBackups as $old) {
            $oldFile = $backupDir . '/' . $old->filename;
            if (file_exists($oldFile)) {
                File::delete($oldFile);
            }
            $old->delete();
        }

        $this->info("Backup complete: {$filename} ({$filesize})");
    } else {
        $this->error('Source database file not found.');
    }
})->purpose('Run automated scheduled database backup');

// Schedule automatic backup based on platform configuration
Schedule::command('platform:backup')->dailyAt('02:00');

