<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_photos', function (Blueprint $table) {
            $table->uuid('id_foto')->primary();
            $table->uuid('id_laporan');
            // Satu sumber kebenaran untuk lokasi foto: file fisik di disk
            // 'local' (private, lihat ReportController@photo untuk cara
            // servenya). Tidak lagi menyimpan salinan base64 di kolom
            // terpisah (photo_data) karena itu cuma duplikasi data yang
            // membengkakkan tabel dan bisa desync dari file aslinya.
            $table->string('photo_path');
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->foreign('id_laporan')->references('id_laporan')->on('reports')->cascadeOnDelete();

            $table->index('id_laporan', 'idx_report_photos_id_laporan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_photos');
    }
};