<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// "Fasilitas & Sarana Penunjang" sekarang disimpan di database (daftar teks dalam JSON) dan diisi admin lewat form.
// Sebelumnya daftar ini dibuat otomatis oleh kode dari nama/jenis fasilitas; agar fasilitas yang sudah ada
// tidak kehilangan data, aturan lama dipakai SEKALI di sini untuk mengisi kolom barunya.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->json('amenities')->nullable()->after('description');
        });

        DB::table('facilities')->orderBy('id_fasilitas')->each(function ($f) {
            DB::table('facilities')
                ->where('id_fasilitas', $f->id_fasilitas)
                ->update(['amenities' => json_encode($this->saranaAwal($f->facility_name, $f->type), JSON_UNESCAPED_UNICODE)]);
        }, 100);
    }

    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn('amenities');
        });
    }

    // Salinan aturan lama (dulu ada di model Facility::getAmenitiesAttribute)
    private function saranaAwal(?string $nama, ?string $jenis): array
    {
        $name = strtolower($nama ?? '');
        $type = strtolower($jenis ?? '');

        if (str_contains($name, 'perpustakaan') || str_contains($name, 'coworking')) {
            return ['Wi-Fi Cepat', 'AC Dingin', 'Stopkontak', 'Area Baca Senyap'];
        }
        if (str_contains($name, 'komputer') || str_contains($name, 'jaringan') || str_contains($name, 'iot') || str_contains($type, 'laboratorium')) {
            return ['PC High-End', 'LAN Gigabit', 'AC Sentral', 'Proyektor'];
        }
        if (str_contains($type, 'olahraga') || str_contains($name, 'futsal') || str_contains($name, 'basket') || str_contains($name, 'badminton') || str_contains($name, 'arena')) {
            return ['Tribun Penonton', 'Lampu Lapangan', 'Kamar Ganti', 'Toilet Bersih'];
        }
        if (str_contains($type, 'aula') || str_contains($type, 'gedung') || str_contains($name, 'auditorium') || str_contains($name, 'gsg')) {
            return ['Sound System Pro', 'Panggung Utama', 'Mic Wireless', 'Kapasitas Besar'];
        }
        if (str_contains($name, 'podcast') || str_contains($name, 'studio')) {
            return ['Kedap Suara', 'Mic Condenser', 'Kamera 4K', 'Lighting Studio'];
        }
        if (str_contains($name, 'masjid')) {
            return ['Tempat Wudhu Luas', 'AC Masjid', 'Sound System', 'Sajadah Bersih'];
        }
        if (str_contains($name, 'rusunawa') || str_contains($name, 'rumah susun') || str_contains($name, 'asrama')) {
            return ['Kamar Mandi Dalam', 'Dapur Bersama', 'Wi-Fi Kampus', 'Keamanan 24 Jam'];
        }
        if (str_contains($name, 'taman')) {
            return ['Gazebo Teduh', 'Pedestrian', 'Pencahayaan Taman', 'Area Santai'];
        }

        return ['AC', 'Wi-Fi', 'Proyektor', 'Papan Tulis'];
    }
};
