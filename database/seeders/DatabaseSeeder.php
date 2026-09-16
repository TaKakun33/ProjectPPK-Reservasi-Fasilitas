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
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin - reservasi',
                'role' => UserRole::Admin,
                'password' => bcrypt('password'),
                'account_status' => 'verified',
            ]
        );

        // Membuat Akun Petugas
        User::firstOrCreate(
            ['email' => 'petugas@example.com'],
            [
                'name' => 'Petugas Fasilitas',
                'role' => UserRole::Petugas,
                'password' => bcrypt('password'),
                'account_status' => 'verified',
            ]
        );

        // Membuat Akun Pengguna Biasa
        User::firstOrCreate(
            ['email' => 'ferry@example.com'],
            [
                'name' => 'Ali Makan Ferry',
                'role' => UserRole::Pengguna,
                'password' => bcrypt('password'),
                'account_status' => 'verified',
            ]
        );

        // Data Contoh Fasilitas Kampus
        $facilities = [
            [
                'facility_name' => 'Laboratorium Komputer 1',
                'type' => 'Laboratorium',
                'location' => 'Gedung A Lantai 2',
                'capacity' => 40,
                'description' => 'Laboratorium komputer lengkap dengan 40 PC spesifikasi tinggi, proyektor, dan AC.',
                'facility_status' => 'aktif',
                'is_active' => true,
            ],
            [
                'facility_name' => 'Laboratorium Jaringan & IoT',
                'type' => 'Laboratorium',
                'location' => 'Gedung Informatika Lantai 3',
                'capacity' => 35,
                'description' => 'Lab khusus praktikum jaringan komputer, mikrokontroler, switch Cisco, dan perangkat IoT.',
                'facility_status' => 'aktif',
                'is_active' => true,
            ],
            [
                'facility_name' => 'Ruang Seminar Utama',
                'type' => 'Ruang Seminar',
                'location' => 'Gedung Rektorat Lantai 3',
                'capacity' => 100,
                'description' => 'Ruang seminar berkapasitas besar dengan sound system profesional dan mic wireless.',
                'facility_status' => 'aktif',
                'is_active' => true,
            ],
            [
                'facility_name' => 'Auditorium Kampus Utama',
                'type' => 'Aula',
                'location' => 'Gedung Convention Center',
                'capacity' => 500,
                'description' => 'Auditorium megah berstandar internasional untuk wisuda, kuliah akbar, dan konser musik kampus.',
                'facility_status' => 'aktif',
                'is_active' => true,
            ],
            [
                'facility_name' => 'Aula Serbaguna Mahasiswa',
                'type' => 'Aula',
                'location' => 'Gedung Kemahasiswaan Lantai 1',
                'capacity' => 250,
                'description' => 'Aula serbaguna untuk kegiatan ormawa, seminar umum, dan pameran karya.',
                'facility_status' => 'aktif',
                'is_active' => true,
            ],
            [
                'facility_name' => 'Lapangan Futsal Indoor',
                'type' => 'Olahraga',
                'location' => 'Sport Center Kampus',
                'capacity' => 20,
                'description' => 'Lapangan futsal rumput sintetis dengan penerangan standar turnamen.',
                'facility_status' => 'aktif',
                'is_active' => true,
            ],
            [
                'facility_name' => 'Lapangan Basket Outdoor',
                'type' => 'Olahraga',
                'location' => 'Area Terbuka Olahraga Barat',
                'capacity' => 25,
                'description' => 'Lapangan basket luar ruangan dengan lantai aspal halus dan ring standar perbasi.',
                'facility_status' => 'aktif',
                'is_active' => true,
            ],
            [
                'facility_name' => 'Lapangan Badminton Indoor A',
                'type' => 'Olahraga',
                'location' => 'Sport Center Lantai 2',
                'capacity' => 12,
                'description' => 'Lapangan bulu tangkis lantai karpet vinil standar PBSI dengan jaring dan pencahayaan optimal.',
                'facility_status' => 'aktif',
                'is_active' => true,
            ],
            [
                'facility_name' => 'Ruang Rapat Senat Akademik',
                'type' => 'Ruang Rapat',
                'location' => 'Gedung Rektorat Lantai 2',
                'capacity' => 30,
                'description' => 'Ruang rapat VIP meja bundar dilengkapi smart TV 75 inch, video conference, dan mikrofon meja.',
                'facility_status' => 'aktif',
                'is_active' => true,
            ],
            [
                'facility_name' => 'Studio Podcast & Multimedia',
                'type' => 'Studio',
                'location' => 'Gedung D Lantai 1',
                'capacity' => 10,
                'description' => 'Studio kedap suara dengan set mikrofon Shure, mixer audio, pencahayaan studio, dan kamera 4K.',
                'facility_status' => 'aktif',
                'is_active' => true,
            ],
            [
                'facility_name' => 'Coworking Space Perpustakaan',
                'type' => 'Ruang Diskusi',
                'location' => 'Perpustakaan Pusat Lantai 2',
                'capacity' => 50,
                'description' => 'Ruang kolaborasi modern mahasiswa dengan colokan di setiap meja, Wi-Fi super cepat, dan whiteboard.',
                'facility_status' => 'aktif',
                'is_active' => true,
            ],
            [
                'facility_name' => 'Smart Classroom 301',
                'type' => 'Ruang Kelas',
                'location' => 'Gedung B Lantai 3',
                'capacity' => 45,
                'description' => 'Ruang kelas interaktif dengan smart board touchscreen, proyektor laser, dan kursi kuliah ergonomis.',
                'facility_status' => 'aktif',
                'is_active' => true,
            ],
        ];

        foreach ($facilities as $facility) {
            \App\Models\Facility::firstOrCreate(
                ['facility_name' => $facility['facility_name']],
                $facility
            );
        }

        // Data contoh kategori laporan kerusakan (modul Laporan - Akbar)
        $this->call(ReportCategorySeeder::class);
    }
}