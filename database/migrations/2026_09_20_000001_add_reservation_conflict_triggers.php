<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * ReservationController & Petugas/ReservationController sudah mengunci
 * baris terkait (lockForUpdate) di dalam DB::transaction sebelum insert/
 * update, jadi race condition di jalur normal aplikasi sudah aman. Tapi
 * itu murni logic aplikasi: apa pun yang insert/update langsung ke tabel
 * reservations di luar dua controller itu (artisan tinker, seeder, query
 * builder di endpoint baru yang lupa pakai service ini, dst) bisa
 * membuat dua reservasi 'approved'/'pending' bentrok tanpa terhalang
 * apa pun.
 *
 * Trigger ini adalah pengaman terakhir di level database: siapa pun/apa
 * pun yang insert/update reservations, kalau hasilnya bentrok jadwal di
 * fasilitas+tanggal yang sama, MySQL akan menolak dengan error, bukan
 * cuma berharap semua kode aplikasi selalu lewat jalur yang benar.
 *
 * Catatan: DB::unprepared mengirim seluruh string sebagai SATU statement
 * lewat PDO, jadi titik koma di dalam BEGIN...END aman tanpa perlu
 * DELIMITER (itu cuma kebutuhan mysql CLI, bukan PDO).
 */
return new class extends Migration
{
    public function up(): void
    {
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
        DB::unprepared('DROP TRIGGER IF EXISTS trg_reservations_no_conflict_ins');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_reservations_no_conflict_upd');
    }
};
