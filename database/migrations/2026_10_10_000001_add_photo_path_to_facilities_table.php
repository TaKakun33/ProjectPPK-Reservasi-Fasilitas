<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Foto utama fasilitas yang diunggah admin (opsional). Bila kosong, tampilan memakai foto bawaan sesuai jenis fasilitas.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};
