<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

// Foto fasilitas disimpan per baris di database (maksimal 5 per fasilitas, dijaga di aplikasi).
// Berkas gambarnya ada di disk privat; kolom photo_path menyimpan lokasinya.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_photos', function (Blueprint $table) {
            $table->uuid('id_foto')->primary();
            $table->uuid('id_fasilitas');
            $table->string('photo_path');
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->foreign('id_fasilitas')->references('id_fasilitas')->on('facilities')->cascadeOnDelete();

            $table->index(['id_fasilitas', 'urutan'], 'idx_facility_photos_fasilitas_urutan');
        });

        // Pindahkan foto tunggal dari kolom facilities.photo_path (bila sudah sempat dipakai), lalu hapus kolomnya
        if (Schema::hasColumn('facilities', 'photo_path')) {
            DB::table('facilities')->whereNotNull('photo_path')->orderBy('id_fasilitas')->each(function ($f) {
                DB::table('facility_photos')->insert([
                    'id_foto'      => (string) Str::uuid(),
                    'id_fasilitas' => $f->id_fasilitas,
                    'photo_path'   => $f->photo_path,
                    'urutan'       => 0,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }, 100);

            Schema::table('facilities', function (Blueprint $table) {
                $table->dropColumn('photo_path');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_photos');

        if (! Schema::hasColumn('facilities', 'photo_path')) {
            Schema::table('facilities', function (Blueprint $table) {
                $table->string('photo_path')->nullable()->after('description');
            });
        }
    }
};
