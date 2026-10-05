<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Petugas yang memutuskan apakah penanganan laporan ini menutup fasilitas
            // (status "dalam perbaikan"). Sebelumnya status 'diproses' otomatis menutup fasilitas.
            $table->boolean('menutup_fasilitas')->default(false)->after('report_status');
        });

        // Pertahankan perilaku lama untuk data yang sudah ada: laporan 'diproses'
        // sebelumnya sudah membuat fasilitasnya masuk perbaikan.
        DB::table('reports')->where('report_status', 'diproses')->update(['menutup_fasilitas' => true]);
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('menutup_fasilitas');
        });
    }
};
