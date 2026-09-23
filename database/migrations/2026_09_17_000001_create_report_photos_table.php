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
            $table->string('photo_path')->nullable();
            $table->longText('photo_data')->nullable();
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