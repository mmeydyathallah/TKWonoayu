<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['name' => 'admin'],
            [
                'email' => 'admin@tkwonoayu.sch.id',
                'password' => Hash::make('admintkwonoayu'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
