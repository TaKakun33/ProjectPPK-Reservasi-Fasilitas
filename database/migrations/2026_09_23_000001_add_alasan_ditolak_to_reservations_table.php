<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // US #9: saat petugas menolak reservasi, wajib isi alasan biar
            // pengguna tahu kenapa reservasinya ditolak (bukan cuma status
            // "ditolak" tanpa keterangan). Diisi bareng reservation_status
            // di ReservationController@reject.
            $table->text('alasan_ditolak')->nullable()->after('cancellation_reason');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('alasan_ditolak');
        });
    }
};
