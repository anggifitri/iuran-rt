<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PlatformBackup;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupPlatformController extends Controller
{
    public function index()
    {
        return redirect()->route('super_admin.settings.index', ['tab' => 'backup']);
    }

    public function createBackup(Request $request)
    {
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d_His');
        $filename = "nexanest_backup_{$timestamp}.sqlite";
        $destPath = $backupDir . '/' . $filename;
        $dbDriver = DB::getDriverName();

        try {
            if ($dbDriver === 'sqlite') {
                $sourcePath = database_path('database.sqlite');
                if (file_exists($sourcePath)) {
                    File::copy($sourcePath, $destPath);
                    $bytes = filesize($destPath);
                    $filesize = round($bytes / 1024 / 1024, 2) . ' MB';

                    $backup = PlatformBackup::create([
                        'filename' => $filename,
                        'filesize' => $filesize,
                        'type' => 'database',
                        'status' => 'success',
                        'notes' => 'Cadangan database SQLite instan via Super Admin HQ',
                        'created_by' => Auth::id(),
                    ]);

                    AuditLogger::log(
                        'DATABASE_BACKUP_CREATED',
                        'Super Admin Platform',
                        $backup->id,
                        "Super Admin (" . Auth::user()->name . ") membuat cadangan database: {$filename} ({$filesize})",
                        null,
                        ['filename' => $filename, 'size' => $filesize],
                        null,
                        'success',
                        'BACKUP'
                    );

                    return back()->with('success', "Cadangan database berhasil dibuat: {$filename} ({$filesize}).");
                }
            }
            return back()->with('error', 'Gagal membuat cadangan database: file sumber tidak ditemukan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuat cadangan: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        $backup = PlatformBackup::findOrFail($id);
        $filePath = storage_path('app/backups/' . $backup->filename);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File cadangan tidak ditemukan di server.');
        }

        AuditLogger::log(
            'DATABASE_BACKUP_DOWNLOADED',
            'Super Admin Platform',
            $backup->id,
            "Super Admin (" . Auth::user()->name . ") mengunduh file cadangan: {$backup->filename}",
            null,
            null,
            null,
            'warning',
            'BACKUP_DOWNLOAD'
        );

        return response()->download($filePath);
    }
}
