<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat Akun Admin
        User::factory()->create([
            'name' => 'Admin PPK',
            'email' => 'admin@example.com',
            'role' => UserRole::Admin,
            'password' => bcrypt('password'),
            'account_status' => 'verified',
        ]);

        // Membuat Akun Petugas
        User::factory()->create([
            'name' => 'Petugas Fasilitas',
            'email' => 'petugas@example.com',
            'role' => UserRole::Petugas,
            'password' => bcrypt('password'),
            'account_status' => 'verified',
        ]);

        // Membuat Akun Pengguna Biasa
        // (sebelumnya role di sini ditulis 'user', tidak konsisten dengan
        // 'pengguna' yang dipakai di RegisteredUserController — sudah diseragamkan)
        User::factory()->create([
            'name' => 'Ali Makan Ferry',
            'email' => 'ferry@example.com',
            'role' => UserRole::Pengguna,
            'password' => bcrypt('password'),
            'account_status' => 'verified',
        ]);
    }
}