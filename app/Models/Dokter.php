<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Dokter extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'nama_dokter',
        'email',
        'alamat',
        'no_hp',
        'username',
        'password',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function pasien()
    {
        return $this->hasMany(DataPasien::class, 'dokter_id');
    }
}
