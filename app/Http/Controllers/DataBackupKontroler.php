<?php

namespace App\Http\Controllers;

use App\Models\DataBackup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class DataBackupKontroler extends Controller
{
    public function index()
    {
        $backups = DataBackup::latest()->get();
        return view('admin.data-backup', compact('backups'));
    }

    public function backup()
    {
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbHost = env('DB_HOST', '127.0.0.1');

        $timestamp = now()->format('Ymd_His');
        $folderTemp = storage_path("app/temp_backup_$timestamp");
        $backupFolder = 'C:\\Backupdata\\';
        $fileRar = $backupFolder . "backup_{$timestamp}.rar";

        // Buat folder tujuan kalau belum ada
        if (!File::exists($backupFolder)) {
            File::makeDirectory($backupFolder, 0777, true);
        }

        // Buat folder sementara untuk backup
        File::makeDirectory($folderTemp, 0775, true, true);

        // Export database ke file .sql
        $sqlFile = "$folderTemp\\database_$timestamp.sql";
        $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > \"$sqlFile\"";
        exec($command);

        // Salin file penting
        File::copyDirectory(public_path('assets'), "$folderTemp\\assets");
        File::copyDirectory(storage_path('app/public'), "$folderTemp\\storage_public");

        // Kompres pakai WinRAR (pastikan path rar.exe benar)
        $rarPath = '"C:\\Program Files\\WinRAR\\Rar.exe"';
        $compressCmd = "$rarPath a -r -ep1 \"$fileRar\" \"$folderTemp\\*\"";
        exec($compressCmd);

        // Ambil ukuran file hasil backup
        $fileSize = file_exists($fileRar) ? filesize($fileRar) : 0;

        // Simpan log ke database
        DataBackup::create([
            'file_name' => basename($fileRar),
            'file_path' => $fileRar,
            'file_size' => number_format($fileSize / 1024 / 1024, 2) . ' MB',
            'created_by' => Auth::guard('admin')->user()->name ?? 'System',
            'status' => file_exists($fileRar) ? 'Sukses' : 'Gagal',
        ]);

        // Hapus folder sementara
        File::deleteDirectory($folderTemp);

        return redirect()->back()->with('success', 'Backup berhasil dibuat dan disimpan di C:\\Backupdata\\');
    }
}
