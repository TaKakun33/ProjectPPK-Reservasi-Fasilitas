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

        // Pengaman terakhir di level database terhadap bentrok jadwal.
        // ReservationController & Petugas/ReservationController sudah
        // mengunci baris terkait (lockForUpdate) di dalam DB::transaction
        // sebelum insert/update, jadi race condition di jalur normal
        // aplikasi sudah aman. Tapi itu murni logic aplikasi: apa pun yang
        // insert/update langsung ke tabel reservations di luar dua
        // controller itu (artisan tinker, seeder, query builder di endpoint
        // baru yang lupa pakai service ini, dst) bisa membuat dua reservasi
        // 'approved'/'pending' bentrok tanpa terhalang apa pun.
        //
        // Trigger ini memastikan siapa pun/apa pun yang insert/update
        // reservations, kalau hasilnya bentrok jadwal di fasilitas+tanggal
        // yang sama, MySQL akan menolak dengan error, bukan cuma berharap
        // semua kode aplikasi selalu lewat jalur yang benar.
        //
        // Catatan: DB::unprepared mengirim seluruh string sebagai SATU
        // statement lewat PDO, jadi titik koma di dalam BEGIN...END aman
        // tanpa perlu DELIMITER (itu cuma kebutuhan mysql CLI, bukan PDO).
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