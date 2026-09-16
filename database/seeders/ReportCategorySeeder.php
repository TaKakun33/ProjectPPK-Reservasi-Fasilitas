<?php

namespace Database\Seeders;

use App\Models\ReportCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed data kategori laporan kerusakan fasilitas.
     */
    public function run(): void
    {
        $categories = [
            'Kerusakan Listrik',
            'Kerusakan AC / Pendingin',
            'Kerusakan Perangkat IT / Komputer',
            'Kerusakan Furnitur & Perabot',
            'Kerusakan Plumbing / Sanitasi',
            'Kerusakan Bangunan / Struktur',
            'Kebersihan & Lingkungan',
            'Lainnya',
        ];

        foreach ($categories as $category) {
            ReportCategory::firstOrCreate(
                ['category_name' => $category],
                ['is_active' => true]
            );
        }
    }
}