<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_photos');
    }
};
