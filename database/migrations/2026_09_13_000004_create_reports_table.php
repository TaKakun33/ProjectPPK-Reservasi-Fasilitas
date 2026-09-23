<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id_laporan')->primary();
            $table->uuid('id_user');
            $table->uuid('id_fasilitas');
            $table->uuid('id_kategori');
            $table->text('description');
            $table->string('report_status', 50)->default('baru');
            $table->text('resolution_notes')->nullable();
            $table->uuid('handled_by')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->restrictOnDelete();
            $table->foreign('id_fasilitas')->references('id_fasilitas')->on('facilities')->restrictOnDelete();
            $table->foreign('id_kategori')->references('id_kategori')->on('report_categories')->restrictOnDelete();
            $table->foreign('handled_by')->references('id_user')->on('users')->nullOnDelete();

            $table->index('id_fasilitas', 'idx_reports_id_fasilitas');
            $table->index('id_kategori', 'idx_reports_id_kategori');
            $table->index('id_user', 'idx_reports_id_user');
            $table->index('report_status', 'idx_reports_report_status');
        });

        // Kunci nilai report_status ke daftar yang benar-benar dipakai
        // aplikasi, biar tidak ada "magic string" nyasar dari luar Eloquent.
        // Foto laporan disimpan di tabel report_photos (mendukung banyak
        // foto per laporan), bukan sebagai kolom di sini.
        DB::statement("ALTER TABLE reports ADD CONSTRAINT chk_reports_report_status CHECK (report_status IN ('baru', 'diproses', 'selesai', 'ditolak'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
