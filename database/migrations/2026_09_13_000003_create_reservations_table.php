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

        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_time_order CHECK (end_time > start_time)');
        DB::statement("ALTER TABLE reservations ADD CONSTRAINT chk_reservations_operating_hours CHECK (start_time >= '07:00:00' AND end_time <= '20:00:00')");
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_start_slot CHECK (SECOND(start_time) = 0 AND MINUTE(start_time) MOD 30 = 0)');
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservations_end_slot CHECK (SECOND(end_time) = 0 AND MINUTE(end_time) MOD 30 = 0)');
        DB::statement("ALTER TABLE reservations ADD CONSTRAINT chk_reservations_reservation_status CHECK (reservation_status IN ('pending', 'approved', 'rejected', 'cancelled'))");

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_reservations_no_conflict_ins
            BEFORE INSERT ON reservations
            FOR EACH ROW
            BEGIN
                DECLARE conflict_count INT;

                IF NEW.reservation_status IN ('pending', 'approved') THEN
                    SELECT COUNT(*) INTO conflict_count
                    FROM reservations
                    WHERE id_fasilitas = NEW.id_fasilitas
                      AND date = NEW.date
                      AND reservation_status IN ('pending', 'approved')
                      AND start_time < NEW.end_time
                      AND end_time > NEW.start_time;

                    IF conflict_count > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Jadwal bentrok dengan reservasi lain pada fasilitas dan tanggal yang sama.';
                    END IF;
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_reservations_no_conflict_upd
            BEFORE UPDATE ON reservations
            FOR EACH ROW
            BEGIN
                DECLARE conflict_count INT;

                IF NEW.reservation_status IN ('pending', 'approved') THEN
                    SELECT COUNT(*) INTO conflict_count
                    FROM reservations
                    WHERE id_fasilitas = NEW.id_fasilitas
                      AND date = NEW.date
                      AND reservation_status IN ('pending', 'approved')
                      AND id_reservasi != NEW.id_reservasi
                      AND start_time < NEW.end_time
                      AND end_time > NEW.start_time;

                    IF conflict_count > 0 THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Jadwal bentrok dengan reservasi lain pada fasilitas dan tanggal yang sama.';
                    END IF;
                END IF;
            END
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_reservations_no_conflict_upd');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_reservations_no_conflict_ins');
        Schema::dropIfExists('reservations');
    }
};