<?php

namespace App\Http\Controllers;

use App\Models\DataBackup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\DataPasien;

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
        $fileZip = storage_path("app/backup_$timestamp.zip");

        // Buat folder sementara
        File::makeDirectory($folderTemp, 0775, true, true);

        // Export database
        $sqlFile = "$folderTemp/database_$timestamp.sql";
        $command = "mysqldump --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > \"$sqlFile\"";
        exec($command);

        // Copy asset
        File::copyDirectory(public_path('assets'), "$folderTemp/assets");
        File::copyDirectory(storage_path('app/public'), "$folderTemp/storage_public");

        // ZIP (built-in Laravel)
        $zip = new \ZipArchive();
        if ($zip->open($fileZip, \ZipArchive::CREATE) === TRUE) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($folderTemp),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath    = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($folderTemp) + 1);

                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();
        }

        // Hapus folder temp
        File::deleteDirectory($folderTemp);

        // Catat log
        $fileSize = filesize($fileZip);
        DataBackup::create([
            'file_name' => "backup_$timestamp.zip",
            'file_path' => $fileZip,
            'file_size' => number_format($fileSize / 1024 / 1024, 2) . ' MB',
            'created_by' => Auth::guard('admin')->user()->name ?? 'System',
            'status' => 'Sukses',
        ]);

        // 👉 Langsung download ke komputer admin
        return response()->download($fileZip)->deleteFileAfterSend(true);
    }


    public function resetSystem()
    {
        $admin = Auth::guard('admin')->user();

        // Pastikan hanya developer yang bisa reset
        if (!$admin || $admin->role !== 'developer') {
            abort(403, 'Anda tidak memiliki izin untuk melakukan reset sistem.');
        }

        try {
            // Matikan foreign key sementara agar truncate tidak error
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Hapus semua data pasien
            DataPasien::truncate();

            // Aktifkan kembali foreign key
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Lokasi folder tempat file pasien tersimpan
            $buktiPembayaranPath = storage_path('app/public/bukti_pembayaran');
            $sktmPath = storage_path('app/public/sktm');
            $videoBeforePath = storage_path('app/public/video_before');
            $videoAfterPath = storage_path('app/public/video_after');

            // Hapus semua folder yang berisi file pasien
            File::deleteDirectory($buktiPembayaranPath);
            File::deleteDirectory($sktmPath);
            File::deleteDirectory($videoBeforePath);
            File::deleteDirectory($videoAfterPath);

            // Buat ulang folder agar tidak error di upload berikutnya
            File::makeDirectory($buktiPembayaranPath, 0775, true);
            File::makeDirectory($sktmPath, 0775, true);
            File::makeDirectory($videoBeforePath, 0775, true);
            File::makeDirectory($videoAfterPath, 0775, true);

            return redirect()->back()->with('success', '✅ Sistem berhasil direset! Semua data pasien dan file terkait telah dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat reset sistem: ' . $e->getMessage());
        }
    }


}
