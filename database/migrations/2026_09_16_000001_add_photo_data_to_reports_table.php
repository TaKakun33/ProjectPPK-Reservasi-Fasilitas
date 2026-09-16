<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Simpan foto sebagai base64 langsung di DB (selain path filenya
            // di kolom 'photo'), supaya foto tetap bisa ditampilkan walaupun
            // symlink storage belum/gagal dibuat di server.
            $table->longText('photo_data')->nullable()->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('photo_data');
        });
    }
};
