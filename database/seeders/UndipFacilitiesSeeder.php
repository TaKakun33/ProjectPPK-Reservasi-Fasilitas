<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class UndipFacilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = [
            [
                'facility_name' => 'Perpustakaan Undip',
                'type' => 'Perpustakaan',
                'location' => 'Gedung UPT Perpustakaan & Undip Press, Kampus Tembalang',
                'capacity' => 600,
                'description' => 'Pusat layanan literasi ilmiah dengan ruang baca terpadu, koleksi buku fisik dan e-journal internasional, ruang multimedia, dan area belajar mandiri.',
                'amenities' => ['Wi-Fi Cepat', 'AC Dingin', 'Stopkontak', 'Area Baca Senyap', 'Ruang Multimedia', 'Akses E-Journal'],
                'facility_status' => 'aktif',
            ],
            [
                'facility_name' => 'Laboratorium Terpadu',
                'type' => 'Laboratorium',
                'location' => 'Gedung Laboratorium Terpadu, Kampus Tembalang',
                'capacity' => 150,
                'description' => 'Pusat riset dan pengujian multidisiplin sains dan rekayasa dengan peralatan instrumentasi canggih berstandar akreditasi.',
                'amenities' => ['Peralatan Instrumentasi', 'AC Sentral', 'Wi-Fi Kampus', 'Ruang Persiapan Sampel'],
                'facility_status' => 'aktif',
            ],
            [
                'facility_name' => 'Arena Olahraga',
                'type' => 'Olahraga',
                'location' => 'Kompleks Stadion & Gelanggang Olahraga Undip, Tembalang',
                'capacity' => 1500,
                'description' => 'Stadion olahraga terbuka berstandar nasional yang dilengkapi lintasan atletik sintetis, lapangan sepak bola rumput alami, dan tribun penonton.',
                'amenities' => ['Tribun Penonton', 'Lintasan Atletik', 'Lampu Lapangan', 'Kamar Ganti', 'Toilet Bersih'],
                'facility_status' => 'aktif',
            ],
            [
                'facility_name' => 'Rumah Susun Mahasiswa',
                'type' => 'Asrama',
                'location' => 'Kawasan Rusunawa Mahasiswa Undip, Tembalang',
                'capacity' => 400,
                'description' => 'Fasilitas hunian mahasiswa nyaman dan aman, dilengkapi ruang komunal, mushola, dapur bersama, akses internet, dan keamanan 24 jam.',
                'amenities' => ['Kamar Mandi Dalam', 'Dapur Bersama', 'Wi-Fi Kampus', 'Keamanan 24 Jam', 'Mushola', 'Ruang Komunal'],
                'facility_status' => 'aktif',
            ],
            [
                'facility_name' => 'Masjid Kampus',
                'type' => 'Tempat Ibadah',
                'location' => 'Kawasan Masjid Kampus Undip, Tembalang',
                'capacity' => 3000,
                'description' => 'Pusat kegiatan keislaman dan peribadatan civitas akademika dengan ruang sholat utama yang megah, ruang seminar keagamaan, dan perpustakaan islam.',
                'amenities' => ['Tempat Wudhu Luas', 'AC Masjid', 'Sound System', 'Sajadah Bersih', 'Perpustakaan Islam'],
                'facility_status' => 'aktif',
            ],
            [
                'facility_name' => 'Gedung UNDIP',
                'type' => 'Gedung Pertemuan',
                'location' => 'Kawasan Rektorat Widya Puraya & Prof. Soedarto, S.H.',
                'capacity' => 2000,
                'description' => 'Gedung utama representatif universitas untuk sidang senat terbuka, wisuda sarjana, simposium internasional, dan acara kenegaraan.',
                'amenities' => ['Sound System Pro', 'Panggung Utama', 'Mic Wireless', 'Kapasitas Besar'],
                'facility_status' => 'aktif',
            ],
            [
                'facility_name' => 'Student Center',
                'type' => 'Pusat Kegiatan Mahasiswa',
                'location' => 'Kompleks Student Center Undip, Tembalang',
                'capacity' => 350,
                'description' => 'Pusat pembinaan dan kreativitas mahasiswa yang menaungi ruang kerja sekretariat UKM, gelanggang latihan seni bela diri, dan aula pertunjukan.',
                'amenities' => ['Ruang Sekretariat UKM', 'Gelanggang Latihan', 'Aula Pertunjukan', 'Wi-Fi Kampus'],
                'facility_status' => 'aktif',
            ],
            [
                'facility_name' => 'Taman di UNDIP',
                'type' => 'Area Terbuka',
                'location' => 'Kawasan Taman Inspirasi & Taman Rusa Undip, Tembalang',
                'capacity' => 100,
                'description' => 'Ruang terbuka hijau asri dengan taman rusa, gazebo belajar kelompok, jalur pedestrian teduh, dan area rekreasi ramah lingkungan.',
                'amenities' => ['Gazebo Teduh', 'Pedestrian', 'Pencahayaan Taman', 'Area Santai', 'Taman Rusa'],
                'facility_status' => 'aktif',
            ],
            [
                'facility_name' => 'Gedung Serba Guna',
                'type' => 'Aula',
                'location' => 'Gedung Serba Guna (GSG) Undip, Kampus Tembalang',
                'capacity' => 1200,
                'description' => 'Gedung serbaguna berkapasitas besar untuk kompetisi olahraga tertutup (badminton/basket), pameran inovasi kampus, bursa karir, dan expo UKM.',
                'amenities' => ['Sound System Pro', 'Tribun Penonton', 'Lampu Sorot', 'Kapasitas Besar', 'Toilet Bersih'],
                'facility_status' => 'aktif',
            ],
        ];

        foreach ($facilities as $facility) {
            Facility::firstOrCreate(
                ['facility_name' => $facility['facility_name']],
                $facility
            );
        }
    }
}
