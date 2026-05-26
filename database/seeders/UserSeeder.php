<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat user Guru
        User::create([
            'name' => 'Bu Siti Rahayu',
            'email' => 'siti@tkwonoayu.test',
            'password' => Hash::make('guru123'),
            'role' => 'guru',
        ]);

        User::create([
            'name' => 'Bu Nur Hidayah',
            'email' => 'nur@tkwonoayu.test',
            'password' => Hash::make('guru123'),
            'role' => 'guru',
        ]);

        // Buat user Wali Murid
        User::create([
            'name' => 'Ibu Sari Wulandari',
            'email' => 'ibu.sari@tkwonoayu.test',
            'password' => Hash::make('wali123'),
            'role' => 'wali_murid',
        ]);

        User::create([
            'name' => 'Ibu Rina Kusuma',
            'email' => 'ibu.rina@tkwonoayu.test',
            'password' => Hash::make('wali123'),
            'role' => 'wali_murid',
        ]);

        User::create([
            'name' => 'Admin TK Wonoayu',
            'email' => 'admin@tkwonoayu.test',
            'password' => Hash::make('admin123'),
            'role' => 'guru',
        ]);
    }
}
