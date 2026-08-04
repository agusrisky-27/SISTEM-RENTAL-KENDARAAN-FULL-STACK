<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kendaraan;
use App\Models\Pelanggan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat User (Admin & Staff)
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Staff Rental',
            'username' => 'staff',
            'password' => Hash::make('staff123'),
            'role' => 'staff'
        ]);

        // 2. Buat Data Kendaraan
        Kendaraan::create([
            'nama' => 'Avanza G',
            'merk' => 'Toyota',
            'jenis' => 'Mobil',
            'plat_nomor' => 'B 1234 ABC',
            'harga_sewa' => 350000,
            'status' => 'tersedia'
        ]);

        Kendaraan::create([
            'nama' => 'NMAX 155',
            'merk' => 'Yamaha',
            'jenis' => 'Motor',
            'plat_nomor' => 'D 5678 DEF',
            'harga_sewa' => 150000,
            'status' => 'tersedia'
        ]);
        
        // 3. Buat Data Pelanggan
        Pelanggan::create([
            'nama' => 'Budi Santoso',
            'no_hp' => '081234567890',
            'no_identitas' => '3201012345678901',
            'alamat' => 'Jl. Merdeka No. 10, Bandung'
        ]);
    }
}
