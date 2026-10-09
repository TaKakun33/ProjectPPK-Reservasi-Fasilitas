<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Index pendukung query rekap okupansi/kerusakan:
// - reports.created_at: filter rentang waktu laporan (tanpa whereDate sehingga index bisa dipakai)
// - reservations (reservation_status, date): filter reservasi approved dalam rentang tanggal lintas fasilitas
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->index('created_at', 'idx_reports_created_at');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->index(['reservation_status', 'date'], 'idx_reservations_status_date');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex('idx_reservations_status_date');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('idx_reports_created_at');
        });
    }
};
