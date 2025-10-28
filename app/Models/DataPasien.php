<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPasien extends Model
{
    use HasFactory;

    protected $table = 'data_pasien';

    protected $fillable = [
        'nomor_antrian',
        'nama_pasien',
        'tgl_lahir',
        'jenis_kelamin',
        'nomor_hp',
        'alamat',
        'pendamping',
        'layanan_id',
        'waktu_id',
        'keluhan',
        'kategori_pendaftaran',
        'tgl_kunjungan',
        'bukti_pembayaran',
        'sktm',
        'dokter_id',
        'status_pemeriksaan',
        'status_berkas',
        'catatan_pemeriksaan',
        'catatan_obat',
        'user_id',
        'video_after',
        'video_before',
        'feedback',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Relasi ke jam pelayanan
    public function waktu()
    {
        return $this->belongsTo(JamPelayanan::class, 'waktu_id');
    }

    // Relasi ke dokter
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }

    
}
