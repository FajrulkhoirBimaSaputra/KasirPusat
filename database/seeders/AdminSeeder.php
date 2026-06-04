<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'username' => 'admin', // <-- Ganti 'email' menjadi 'username' (atau kolom yang sesuai)
            'password' => Hash::make('admin123'),
            'role' => 'admin', // Biarkan ini karena dari log kamu terlihat ada kolom 'role'
        ]);
    }
}
