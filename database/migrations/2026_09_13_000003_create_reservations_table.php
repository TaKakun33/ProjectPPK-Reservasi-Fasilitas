<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id_reservasi')->primary();
            $table->uuid('id_user');
            $table->uuid('id_fasilitas');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('purpose', 255);
            $table->string('reservation_status', 50)->default('pending');
            $table->text('cancellation_reason')->nullable();
            // US #9: saat petugas menolak reservasi, wajib isi alasan biar
            // pengguna tahu kenapa reservasinya ditolak (bukan cuma status
            // "ditolak" tanpa keterangan). Diisi bareng reservation_status
            // di ReservationController@reject.
            $table->text('alasan_ditolak')->nullable();
            $table->uuid('processed_by')->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->restrictOnDelete();
            $table->foreign('id_fasilitas')->references('id_fasilitas')->on('facilities')->restrictOnDelete();
            $table->foreign('processed_by')->references('id_user')->on('users')->nullOnDelete();

            $table->index(['id_fasilitas', 'date', 'reservation_status'], 'idx_reservations_fasilitas_date_status');
            $table->index('id_fasilitas', 'idx_reservations_id_fasilitas');
            $table->index('id_user', 'idx_reservations_id_user');
            $table->index('reservation_status', 'idx_reservations_reservation_status');
        });

        // Mirrors the CHECK constraints in the original SQL schema.
        // Note: these are DB-level safety nets only — the 30-minute-slot
        // and operating-hours validation should still live in the
        // Form Request for the reservation store/update endpoints.
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_time_order CHECK (end_time > start_time)');
        DB::statement("ALTER TABLE reservations ADD CONSTRAINT chk_reservations_operating_hours CHECK (start_time >= '07:00:00' AND end_time <= '20:00:00')");
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_start_slot CHECK (SECOND(start_time) = 0 AND MINUTE(start_time) MOD 30 = 0)');
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_end_slot CHECK (SECOND(end_time) = 0 AND MINUTE(end_time) MOD 30 = 0)');

        // Kunci nilai reservation_status ke daftar yang benar-benar dipakai
        // aplikasi, biar tidak ada "magic string" nyasar dari luar Eloquent.
        DB::statement("ALTER TABLE reservations ADD CONSTRAINT chk_reservations_reservation_status CHECK (reservation_status IN ('pending', 'approved', 'rejected', 'cancelled'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
