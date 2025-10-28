<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Dokter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 🔹 Admin Dummy
        Admin::create([
            'nama' => 'Admin Klinik',
            'username' => 'admin123',
            'email' => 'admin@klinik.com',
            'no_hp' => '09887654245',
            'password' => Hash::make('admin1234'),
            'role' => 'master'
        ]);

        Admin::create([
            'nama' => 'developer',
            'username' => 'developer',
            'email' => 'developer@syn.com',
            'no_hp' => '0987654321',
            'password' => Hash::make('developersyn'),
            'role' => 'developer'
        ]);

        // 🔹 User Dummy
        User::create([
            'name' => 'User Biasa',
            'email' => 'user@example.com',
            'no_hp' => '09887654245',
            'alamat' => 'Jl. Bunga No. 12, Ponorogo',
            'password' => Hash::make('user1234'),
        ]);

        Dokter::create([
            'nama_dokter' => 'Dr. Andi Saputra',
            'email' => 'dokter@klinik.com',
            'alamat' => 'Jl. Sehat No. 99, Ponorogo',
            'no_hp' => '081234567890',
            'username' => 'dokterandi',
            'password' => Hash::make('dokter123'),
        ]);

    }
}
