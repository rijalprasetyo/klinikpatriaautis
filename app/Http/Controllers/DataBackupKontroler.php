<?php

namespace App\Http\Controllers;

use App\Models\DataBackup;
use ZipArchive;
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
        $timestamp = now()->format('Ymd_His');
    
        // Folder temp untuk proses backup
        $tempFolder = storage_path("app/backup_temp_$timestamp");
    
        // Folder penyimpanan ZIP di public_html/public/storage/backup_files
        $backupFolder = public_path("storage/backup_files");
        File::makeDirectory($backupFolder, 0775, true, true);
    
        // File ZIP yang akan dibuat
        $zipPath = $backupFolder . "/backup_$timestamp.zip";
    
        // File SQL di folder temp
        $sqlPath = "$tempFolder/database_$timestamp.sql";
    
        // 1. Buat folder temp
        File::makeDirectory($tempFolder, 0775, true);
    
        /*
        |--------------------------------------------------------------------------
        | 2. BACKUP DATABASE TANPA exec()
        |--------------------------------------------------------------------------
        */
        $pdo = DB::connection()->getPdo();
        $dbName = env('DB_DATABASE');
    
        $sql = "SET FOREIGN_KEY_CHECKS=0;\n\n";
        $tables = DB::select("SHOW TABLES");
        $key = "Tables_in_{$dbName}";
    
        foreach ($tables as $table) {
            $tableName = $table->$key;
    
            $create = DB::select("SHOW CREATE TABLE `$tableName`")[0]->{'Create Table'};
    
            $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";
            $sql .= $create . ";\n\n";
    
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $values = array_map(function ($value) use ($pdo) {
                    return $pdo->quote($value);
                }, (array) $row);
    
                $sql .= "INSERT INTO `$tableName` VALUES(" . implode(',', $values) . ");\n";
            }
            $sql .= "\n\n";
        }
    
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
    
        file_put_contents($sqlPath, $sql);
    
        /*
        |--------------------------------------------------------------------------
        | 3. COPY ASSETS
        |--------------------------------------------------------------------------
        */
    
        // assets → temp/assets
        if (File::exists(public_path('assets'))) {
            File::copyDirectory(public_path('assets'), "$tempFolder/assets");
        }
    
        // === FIX PATH SESUAI PERMINTAAN ===
        // public_html/public/storage/bukti_pembayaran
        if (File::exists(public_path('storage/bukti_pembayaran'))) {
            File::copyDirectory(public_path('storage/bukti_pembayaran'), "$tempFolder/bukti_pembayaran");
        }
    
        if (File::exists(public_path('storage/sktm'))) {
            File::copyDirectory(public_path('storage/sktm'), "$tempFolder/sktm");
        }
    
        if (File::exists(public_path('storage/video_before'))) {
            File::copyDirectory(public_path('storage/video_before'), "$tempFolder/video_before");
        }
    
        if (File::exists(public_path('storage/video_after'))) {
            File::copyDirectory(public_path('storage/video_after'), "$tempFolder/video_after");
        }
    
        /*
        |--------------------------------------------------------------------------
        | 4. ZIP SEMUA FILE
        |--------------------------------------------------------------------------
        */
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
    
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempFolder),
                \RecursiveIteratorIterator::SELF_FIRST
            );
    
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relative = substr($filePath, strlen($tempFolder) + 1);
    
                    $zip->addFile($filePath, $relative);
                }
            }
    
            $zip->close();
        }
    
        $zipSize = filesize($zipPath);
    
        /*
        |--------------------------------------------------------------------------
        | 5. SIMPAN LOG DATABASE
        |--------------------------------------------------------------------------
        */
        DataBackup::create([
            'file_name'  => "backup_$timestamp.zip",
            'file_path'  => $zipPath,
            'file_size'  => number_format($zipSize / 1024 / 1024, 2) . ' MB',
            'created_by' => auth()->guard('admin')->user()->name ?? 'System',
            'status'     => 'Sukses',
        ]);
    
        /*
        |--------------------------------------------------------------------------
        | 6. HAPUS FOLDER TEMPORARY
        |--------------------------------------------------------------------------
        */
        File::deleteDirectory($tempFolder);
    
        /*
        |--------------------------------------------------------------------------
        | 7. DOWNLOAD ZIP KE USER
        |--------------------------------------------------------------------------
        */
        return response()->download($zipPath);
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
            $buktiPembayaranPath = public_path('storage/bukti_pembayaran');
            $sktmPath            = public_path('storage/sktm');
            $videoBeforePath     = public_path('storage/video_before');
            $videoAfterPath      = public_path('storage/video_after');


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
