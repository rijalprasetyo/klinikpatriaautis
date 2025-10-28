<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamPelayanan extends Model
{
    use HasFactory;

    protected $table = 'jam_pelayanan';

    protected $fillable = [
        'jam_mulai',
        'jam_selesai',
    ];

    public function pasien()
    {
        return $this->hasMany(DataPasien::class, 'waktu_id');
    }
}
