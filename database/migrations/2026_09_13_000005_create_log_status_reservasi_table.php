<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_status_reservasi', function (Blueprint $table) {
            $table->uuid('id_log')->primary();
            $table->uuid('id_reservasi');
            $table->string('status_before', 50)->nullable();
            $table->string('status_after', 50);
            $table->uuid('changed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_reservasi')->references('id_reservasi')->on('reservations')->cascadeOnDelete();
            $table->foreign('changed_by')->references('id_user')->on('users')->nullOnDelete();

            $table->index('id_reservasi', 'idx_log_status_reservasi_id_reservasi');
        });

        // Sebelumnya status_before/status_after bebas diisi string apa
        // saja — log audit yang nilainya sendiri tidak terjamin valid jadi
        // kurang berguna untuk audit. Dikunci ke daftar status yang sama
        // dengan reservations.reservation_status. status_before boleh NULL
        // (baris log pertama, saat reservasi baru dibuat).
        DB::statement("ALTER TABLE log_status_reservasi ADD CONSTRAINT chk_log_status_reservasi_before CHECK (status_before IS NULL OR status_before IN ('pending', 'approved', 'rejected', 'cancelled'))");
        DB::statement("ALTER TABLE log_status_reservasi ADD CONSTRAINT chk_log_status_reservasi_after CHECK (status_after IN ('pending', 'approved', 'rejected', 'cancelled'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('log_status_reservasi');
    }
};
