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
            $table->string('facility_status', 50)->default('aktif');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();

            $table->index('facility_status', 'idx_facilities_facility_status');
            $table->index(['type', 'location', 'capacity'], 'idx_facilities_type_location_capacity');
            $table->index('type', 'idx_facilities_type');
        });

        // Mirrors the CHECK constraint in the original SQL schema.
        // Note: this is a DB-level safety net only — server-side validation
        // for capacity should still live in the Form Request, per our earlier discussion.
        DB::statement('ALTER TABLE facilities ADD CONSTRAINT chk_facilities_capacity CHECK (capacity > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
