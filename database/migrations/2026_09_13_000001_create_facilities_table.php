<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->uuid('id_fasilitas')->primary();
            $table->string('facility_name', 100);
            $table->string('type', 50);
            $table->string('location', 150);
            $table->integer('capacity');
            $table->text('description')->nullable();
            // Satu sumber kebenaran untuk state fasilitas. Sebelumnya ada
            // dua kolom (facility_status + is_active) yang berujung dicek
            // bareng ("!$facility->is_active || $facility->facility_status
            // !== 'aktif'") di ReservationController — tanda dua kolom itu
            // sebenarnya menjawab pertanyaan yang sama: "boleh dipakai atau
            // tidak". 'nonaktif' di sini menggantikan is_active=false
            // (dihapus/disembunyikan admin), 'dalam perbaikan' tetap dari
            // modul petugas (kondisi operasional sementara).
            $table->string('facility_status', 50)->default('aktif');
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();

            $table->index('facility_status', 'idx_facilities_facility_status');
            $table->index(['type', 'location', 'capacity'], 'idx_facilities_type_location_capacity');
            // idx_facilities_type dihapus: sudah jadi prefix dari
            // idx_facilities_type_location_capacity (type, location,
            // capacity), jadi query WHERE type = ... tetap kepakaikan index
            // itu. Index terpisah cuma nambah beban tulis tanpa manfaat baca.
        });

        // Mirrors the CHECK constraint in the original SQL schema.
        // Note: this is a DB-level safety net only — server-side validation
        // for capacity should still live in the Form Request, per our earlier discussion.
        DB::statement('ALTER TABLE facilities ADD CONSTRAINT chk_facilities_capacity CHECK (capacity > 0)');

        // Kunci nilai facility_status ke daftar yang benar-benar dipakai
        // aplikasi, biar tidak ada "magic string" nyasar dari luar Eloquent.
        DB::statement("ALTER TABLE facilities ADD CONSTRAINT chk_facilities_facility_status CHECK (facility_status IN ('aktif', 'dalam perbaikan', 'nonaktif'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
