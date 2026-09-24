<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('reservations') && !Schema::hasColumn('reservations', 'alasan_ditolak')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->text('alasan_ditolak')->nullable()->after('cancellation_reason');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('reservations') && Schema::hasColumn('reservations', 'alasan_ditolak')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('alasan_ditolak');
            });
        }
    }
};
