<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataBackup extends Model
{
    use HasFactory;

    // Nama tabel (opsional jika mengikuti konvensi Laravel)
    protected $table = 'data_backup';

    // Kolom yang bisa diisi (mass assignment)
    protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'created_by',
        'status',
    ];

    // (Opsional) Jika ingin menampilkan ukuran file dengan format mudah dibaca
    public function getReadableSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
